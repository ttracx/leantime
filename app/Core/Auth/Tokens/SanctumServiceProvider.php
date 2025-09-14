<?php

namespace Safe4Work\Core\Auth\Tokens;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Contracts\HasAbilities;
use Laravel\Sanctum\Sanctum as SanctumBase;
use Safe4Work\Domain\Auth\Services\AccessToken;

class SanctumServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HasAbilities::class, AccessToken::class);
    }

    public function boot(): void
    {

        // Use our custom token model
        SanctumBase::usePersonalAccessTokenModel(AccessToken::class);

    }
}
