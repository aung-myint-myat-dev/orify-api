<?php

namespace App\Providers;

use App\Contracts\AuthRepositoryInterface;
use App\Models\User;
use App\Repositories\EloquentAuthRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, EloquentAuthRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(
            static fn (User $user, string $token): string => rtrim((string) config('app.url'), '/')
                .'/reset-password?'.http_build_query([
                    'token' => $token,
                    'email' => $user->getEmailForPasswordReset(),
                ])
        );
    }
}
