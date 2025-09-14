<?php

namespace Safe4Work\Domain\Api\Services;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Safe4Work\Core\Events\DispatchesEvents;
use Safe4Work\Domain\Api\Repositories\Api as ApiRepository;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Users\Repositories\Users as UserRepository;
use RangeException;

class Api
{
    use DispatchesEvents;

    private ApiRepository $apiRepository;

    private UserRepository $userRepo;

    private ?array $error = null;

    /**
     * @api
     */
    public function __construct(ApiRepository $apiRepository, UserRepository $userRepo)
    {
        $this->apiRepository = $apiRepository;
        $this->userRepo = $userRepo;
    }

    /**
     * @throws BindingResolutionException
     *
     * @api
     */
    public function getAPIKeyUser(string $apiKey): bool|array
    {

        // Split apiKey into parts
        $apiKeyParts = explode('_', $apiKey);

        if (! is_array($apiKeyParts) || count($apiKeyParts) != 3) {
            return false;
        }

        $namespace = $apiKeyParts[0];
        $user = $apiKeyParts[1];
        $key = $apiKeyParts[2];

        if ($namespace != 'lt') {
            return false;
        }

        $apiUser = $this->apiRepository->getAPIKeyUser($user);

        if ($apiUser) {
            if (password_verify($key, $apiUser['password'])) {

                $this->setApiUserSession($apiUser, true);

                return $apiUser;
            }
        }

        return false;
    }

    /**
     * @return void
     *
     * @throws BindingResolutionException
     *
     * Note: This is deliberately a duplicate of the authService setSession method to not have to load the authService
     * which will run db connections when we are not ready yet.
     * TODO: Move session management into a dedicated service
     */
    public function setApiUserSession(array $user, bool $isExternalAuth = false)
    {

        $currentUser = [
            'id' => (int) $user['id'],
            'name' => strip_tags($user['firstname']),
            'profileId' => $user['profileId'],
            'mail' => filter_var($user['username'], FILTER_SANITIZE_EMAIL),
            'clientId' => $user['clientId'],
            'role' => Roles::getRoleString($user['role']),
            'settings' => $user['settings'] ? unserialize($user['settings']) : [],
            'twoFAEnabled' => $user['twoFAEnabled'] ?? false,
            'twoFAVerified' => false,
            'twoFASecret' => $user['twoFASecret'] ?? '',
            'isExternalAuth' => $isExternalAuth,
            'createdOn' => ! empty($user['createdOn']) ? dtHelper()->parseDbDateTime($user['createdOn']) : dtHelper()->userNow(),
            'modified' => ! empty($user['modified']) ? dtHelper()->parseDbDateTime($user['modified']) : dtHelper()->userNow(),
        ];

        $currentUser = self::dispatch_filter('user_session_vars', $currentUser);

        // Session handler for api is array
        session(['userdata' => $currentUser]);

    }

    /**
     * createAPIKey - simple service wrapper to create a new user
     *
     * TODO: Should accept userModel
     *
     * @param array $values basic user values

     * @return bool|array returns new user id on success, false on failure

     *
     * @throws Exception
     *
     * @api
     */
    public function createAPIKey(array $values): bool|array
    {
        $user = $this->randomStr(32);
        $password = $this->randomStr(32);

        $values['user'] = $user;
        $values['lastname'] = '';
        $values['passwordClean'] = $password;
        $values['password'] = $password;
        $values['status'] = 'a';
        $values['clientId'] = '';
        $values['phone'] = '';
        $values['id'] = $this->userRepo->addUser($values);

        return $values['id'] ? $values : false;
    }

    /**
     * getAPIKeys - gets api keys (users) from user table
     *
     *
     *
     * @api
     */
    public function getAPIKeys(): false|array
    {
        $keys = $this->userRepo->getAllBySource('api');

        foreach ($keys as &$key) {
            $key['username'] = substr($key['username'], 0, 5);
        }

        return $keys;
    }

    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * This function uses type hints now (PHP 7+ only), but it was originally
     * written for PHP 5 as well.
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param  int  $length  How many characters do we want?
     * @param  string  $keyspace  A string of all possible characters to select from
     *
     * @throws Exception
     *
     * @api
     */
    public function randomStr(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new RangeException('Length must be a positive integer');
        }

        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; $i++) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }

    /**
     * @todo Remove this.
     *
     * @see ../Controllers/Tickets.php
     *
     * @api
     */
    public function jsonResponse(int $id, ?array $result): void
    {
        $jsonRPCArray = [
            'jsonrpc' => '2.0',
        ];

        header('Content-Type: application/json; charset=utf-8');

        if ($this->error != null) {
            $jsonRPCArray['error'] = $this->error;
        } elseif ($result !== null) {
            $jsonRPCArray['result'] = $result;
        }

        echo json_encode($jsonRPCArray);
    }

    /**
     * Check the manifest for the asset and serve if found.
     *
     * @api
     */
    public function getCaseCorrectPathFromManifest(string $filepath): string|false
    {
        $manifest = mix('')->getManifest();
        $clone = array_change_key_case(collect(Arr::dot($manifest))
            ->mapWithKeys(fn ($value, $key) => [Str::of($key)->replaceFirst('./', '/')->lower()->toString() => $value])
            ->all());

        if (is_null($referenceValue = $clone[strtolower($filepath)] ?? null)) {
            return false;
        }

        $correctManifest = array_filter($manifest, fn ($arr) => in_array($referenceValue, $arr));
        $basePath = array_keys($correctManifest)[0];
        $correctManifest = array_values($correctManifest)[0];

        return $basePath.array_search($referenceValue, $correctManifest);
    }

    /**
     * @return true
     */
    public function healthCheck()
    {
        return true;
    }
}
