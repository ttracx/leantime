<?php

namespace Safe4Work\Domain\Files\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Db\Db as DbCore;
use Safe4Work\Core\Files\Contracts\FileManagerInterface;
use PDO;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Files
{
    public array $adminModules = ['project' => 'Projects', 'ticket' => 'Tickets', 'client' => 'Clients', 'lead' => 'Lead', 'private' => 'General']; // 'user'=>'Users',

    public array $userModules = ['project' => 'Projects', 'ticket' => 'Tickets', 'private' => 'General'];

    private DbCore $db;

    private FileManagerInterface $fileManager;

    public function __construct(DbCore $db, FileManagerInterface $fileManager)
    {
        $this->db = $db;
        $this->fileManager = $fileManager;
    }

    public function addFile($values, $module): false|string
    {

        $sql = 'INSERT INTO zp_file (
					encName, realName, extension, module, moduleId, userId, date
				) VALUES (
					:encName, :realName, :extension, :module, :moduleId, :userId, NOW()
				)';

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':encName', $values['encName']);
        $stmn->bindValue(':realName', $values['realName']);
        $stmn->bindValue(':extension', $values['extension']);
        $stmn->bindValue(':module', $module);
        $stmn->bindValue(':moduleId', $values['moduleId'], PDO::PARAM_INT);
        $stmn->bindValue(':userId', $values['userId'], PDO::PARAM_INT);

        $stmn->execute();
        $stmn->closeCursor();

        return $this->db->database->lastInsertId();
    }

    public function getFile($id): array|false
    {

        $sql = 'SELECT
					file.id, file.extension, file.realName, file.encName, file.date, file.module, file.moduleId,
					user.firstname, user.lastname
				FROM zp_file as file
				INNER JOIN zp_user as user ON file.userId = user.id
				WHERE file.id=:id';

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':id', $id, PDO::PARAM_INT);

        $stmn->execute();
        $values = $stmn->fetch();
        $stmn->closeCursor();

        return $values;
    }

    public function getFiles(int $userId = 0): false|array
    {

        $sql = 'SELECT
					file.id, file.moduleId, file.extension, file.realName, file.encName, file.date, file.module,
					user.firstname, user.lastname
				FROM zp_file as file
				INNER JOIN zp_user as user ON file.userId = user.id ';

        if ($userId && $userId > 0) {
            $sql .= ' WHERE file.userId = '.$userId;
        }

        $sql .= ' ORDER BY file.module, file.moduleId';

        $stmn = $this->db->database->prepare($sql);
        $stmn->execute();
        $values = $stmn->fetchAll();
        $stmn->closeCursor();

        return $values;
    }

    public function getFolders($module): array
    {

        $folders = [];
        $files = $this->getFiles(session('userdata.id'));

        $sql = match ($module) {
            'ticket' => 'SELECT headline as title, id FROM zp_tickets WHERE id=:moduleId LIMIT 1',
            'client' => 'SELECT name as title, id FROM zp_clients WHERE id=:moduleId LIMIT 1',
            'project' => 'SELECT name as title, id FROM zp_projects WHERE id=:moduleId LIMIT 1',
            'lead' => 'SELECT name as title, id FROM zp_lead WHERE id=:moduleId LIMIT 1',
            default => 'SELECT headline as title, id FROM zp_tickets WHERE id=:moduleId LIMIT 1',
        };

        $stmn = $this->db->database->prepare($sql);

        $ids = [];
        foreach ($files as $file) {
            $stmn->bindValue(':moduleId', $file['moduleId'], PDO::PARAM_STR);
            $stmn->execute();
            if (! isset($ids[$file['moduleId']])) {
                $folders[] = $stmn->fetch();
                $ids[$file['moduleId']] = true;
            }
        }

        $stmn->closeCursor();

        return $folders;
    }

    /**
     * @param  null  $moduleId
     */
    public function getFilesByModule(string $module = '', $moduleId = null, ?int $userId = 0): false|array
    {

        $sql = "SELECT
					file.id,
					file.extension,
					file.realName,
					file.encName,
					file.date,
					DATE_FORMAT(file.date,  '%Y,%m,%e') AS timelineDate,
					file.module,
					file.moduleId,
					user.firstname,
					user.lastname,
					user.id AS userId
				FROM zp_file as file

				INNER JOIN zp_user as user ON file.userId = user.id ";

        if ($module != '') {
            $sql .= ' WHERE file.module=:module ';
        } else {
            $sql .= " WHERE file.module <> '' ";
        }

        if ($moduleId != null) {
            $sql .= ' AND moduleId=:moduleId';
        }

        if ($userId && $userId > 0) {
            $sql .= ' AND userId= :userId';
        }

        $stmn = $this->db->database->prepare($sql);
        if ($module != '') {
            $stmn->bindValue(':module', $module, PDO::PARAM_STR);
        }

        if ($moduleId != null) {
            $stmn->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
        }

        if ($userId && $userId > 0) {
            $stmn->bindValue(':userId', $userId, PDO::PARAM_INT);
        }

        $stmn->execute();
        $values = $stmn->fetchAll();
        $stmn->closeCursor();

        return $values;
    }

    public function deleteFile($id): bool
    {
        $sql = 'SELECT encName, extension FROM zp_file WHERE id=:id';

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':id', $id, PDO::PARAM_INT);

        $stmn->execute();
        $values = $stmn->fetch();
        $stmn->closeCursor();

        if (isset($values['encName']) && isset($values['extension'])) {
            // Use FileManager to delete the file
            $fileName = $values['encName'].'.'.$values['extension'];

            // Try to delete from both public and private storage
            $this->fileManager->deleteFile($fileName, false); // Private storage
            $this->fileManager->deleteFile($fileName, true);  // Public storage
        }

        $sql = 'DELETE FROM zp_file WHERE id=:id';

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':id', $id, PDO::PARAM_INT);

        $result = $stmn->execute();
        $stmn->closeCursor();

        return $result;
    }

    /**
     * @return array|false
     *
     * @throws BindingResolutionException
     */
    public function upload($file, $module, $moduleId): false|string|array
    {
        // Clean module mess
        if ($module === 'projects') {
            $module = 'project';
        }
        if ($module === 'tickets') {
            $module = 'ticket';
        }

        try {
            $uploadedFile = $file['file'];
            $path = $uploadedFile['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $realName = str_replace('.'.$ext, '', $uploadedFile['name']);

            // Just something unique to avoid collision in s3 (each customer has their own folder)
            $newname = md5(session('userdata.id').time());

            // Create a UploadedFile instance
            $symfonyFile = new UploadedFile(
                $uploadedFile['tmp_name'],
                $uploadedFile['name'],
                $uploadedFile['type'],
                $uploadedFile['error'],
                true
            );

            // Use FileManager to upload the file
            $result = $this->fileManager->upload($symfonyFile, $newname, false);

            if ($result !== false) {
                $values = [
                    'encName' => $newname,
                    'realName' => $realName,
                    'extension' => $ext,
                    'moduleId' => $moduleId,
                    'userId' => session('userdata.id'),
                    'module' => $module,
                    'fileId' => '',
                ];

                $fileAddResults = $this->addFile($values, $module);

                if ($fileAddResults) {
                    $values['fileId'] = $fileAddResults;

                    return $values;
                }
            }

            return false;
        } catch (\Exception $e) {
            report($e);

            return $e->getMessage();
        }
    }

    public function uploadCloud($name, $url, $module, $moduleId): void
    {

        // Add cloud stuff ehre.
    }
}
