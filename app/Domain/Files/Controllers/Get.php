<?php

namespace Safe4Work\Domain\Files\Controllers;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Safe4Work\Core\Configuration\Environment;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Files\FileManager;
use Safe4Work\Domain\Files\Repositories\Files as FileRepository;
use Safe4Work\Domain\Files\Services\Files as FileService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Get extends Controller
{
    private FileService $filesService;

    private FileRepository $filesRepo;

    private Environment $config;

    private FileManager $fileManager;

    public function init(
        FileRepository $filesRepo,
        FileService $filesService,
        Environment $config,
        FileManager $fileManager
    ): void {
        $this->filesRepo = $filesRepo;
        $this->filesService = $filesService;
        $this->config = $config;
        $this->fileManager = $fileManager;
    }

    /**
     * @throws \Exception
     */
    public function get(): Response
    {
        $encName = preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['encName']);
        $realName = $_GET['realName'];
        $ext = preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['ext']);
        $module = preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['module'] ?? '');

        // Construct the file name
        $fileName = $encName.'.'.$ext;

        // Use the FileManager to get the file
        $response = $this->fileManager->getFile($fileName, $realName, false);

        if ($response === false) {
            return new Response('File not found', 404);
        }

        return $response;
    }

    /**
     * Retrieves a file locally and returns it as a streamed response.
     *
     * @param  string  $encName  The encoded name of the file.
     * @param  string  $ext  The extension of the file.
     * @param  string  $module  The module of the file.
     * @param  string  $realName  The real name of the file.
     * @return Response The streamed response containing the file or a 404 response if the file was not found.
     */
    private function getFileLocally($encName, $ext, $module, $realName): Response
    {

        $mimes = [
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpg',
            'gif' => 'image/gif',
            'png' => 'image/png',
        ];

        // TODO: Replace with ROOT
        $path = realpath(APP_ROOT.'/'.$this->config->userFilePath.'/');

        $fullPath = $path.'/'.$encName.'.'.$ext;

        if (file_exists(realpath($fullPath))) {
            if ($fd = fopen(realpath($fullPath), 'rb')) {
                $path_parts = pathinfo($fullPath);

                if ($ext == 'pdf') {
                    $mime_type = 'application/pdf';
                    header('Content-type: application/pdf');
                    header('Content-Disposition: inline; filename="'.$realName.'.'.$ext.'"');
                } elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
                    $mime_type = $mimes[$ext];
                    header('Content-type: '.$mimes[$ext]);
                    header('Content-disposition: inline; filename="'.$realName.'.'.$ext.'";');
                } elseif ($ext == 'svg') {
                    $mime_type = 'image/svg+xml';
                    header('Content-type: image/svg+xml');
                    header('Content-disposition: attachment; filename="'.$realName.'.'.$ext.'";');
                } else {
                    $mime_type = 'application/octet-stream';
                    header('Content-type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.$realName.'.'.$ext.'"');
                }

                $sLastModified = filemtime($fullPath);
                $sEtag = md5_file($fullPath);

                $sFileSize = filesize($fullPath);

                $oStreamResponse = new StreamedResponse;
                $oStreamResponse->headers->set('Content-Type', $mime_type);
                $oStreamResponse->headers->set('Content-Length', $sFileSize);
                $oStreamResponse->headers->set('ETag', $sEtag);

                if (app()->make(Environment::class)->debug == false) {
                    $oStreamResponse->headers->set('Pragma', 'public');
                    $oStreamResponse->headers->set('Cache-Control', 'max-age=86400');
                    $oStreamResponse->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', $sLastModified).' GMT');
                } else {
                    Log::warning('Not caching');
                }

                $oStreamResponse->setCallback(function () use ($fullPath) {
                    readfile($fullPath);
                });

                return $oStreamResponse;
            }
        }

        return new Response('File not found', 404);

    }

    /**
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getFileFromS3($encName, $ext, $module, $realName): Response
    {

        $mimes = [
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpg',
            'gif' => 'image/gif',
            'png' => 'image/png',
        ];

        // Instantiate the client.

        $s3Config = [
            'version' => 'latest',
            'region' => $this->config->s3Region,

        ];

        // AWS SDK allows you to connect to aws resource using the role attached to an instance
        if (! empty($this->config->s3Key) && ! empty($this->config->s3Secret)) {
            $s3Config['credentials'] = [
                'key' => $this->config->s3Key,
                'secret' => $this->config->s3Secret,
            ];
        }

        if (
            ! empty($this->config->s3EndPoint)
            && $this->config->s3EndPoint != 'null'
            && $this->config->s3EndPoint != 'false'
        ) {
            $s3Config['endpoint'] = $this->config->s3EndPoint;
        }

        if (($this->config->s3UsePathStyleEndpoint === true
                || $this->config->s3UsePathStyleEndpoint === 'true')
            && ($this->config->s3UsePathStyleEndpoint !== 'false')
        ) {
            $s3Config['use_path_style_endpoint'] = true;
        }

        // Instantiate the S3 client with your AWS credentials
        $s3Client = new S3Client($s3Config);

        try {
            // implode all non-empty elements to allow s3FolderName to be empty.
            // otherwise you will get an error as the key starts with a slash
            $fileName = implode('/', array_filter([$this->config->s3FolderName, $encName.'.'.$ext]));
            $result = $s3Client->getObject([
                'Bucket' => $this->config->s3Bucket,
                'Key' => $fileName,
                'Body' => 'this is the body!',
            ]);

            $response = new Response($result->get('Body')->getContents());

            if ($ext == 'pdf') {
                $response->headers->set('Content-type', 'application/pdf');
            } elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
                $response->headers->set('Content-type', $result['ContentType']);
            } elseif ($ext == 'svg') {
                $response->headers->set('Content-type', 'image/svg+xml');
            } else {
                header('Content-disposition: attachment; filename="'.$realName.'.'.$ext.'";');
            }

            $response->headers->set('Content-Disposition', 'inline; filename="'.$realName.'.'.$ext.'"');

            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'max-age=86400');

            return $response;

        } catch (\Exception $e) {

            Log::error($e);

            return new Response('File cannot be found', 400);
        }
    }
}
