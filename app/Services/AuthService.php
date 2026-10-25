<?php

namespace App\Services;

use App\Contracts\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use LogicException;
use Throwable;

class AuthService
{
    private const DUMMY_PASSWORD_HASH = '$2y$12$v9FJgnTy5CIjXwDdNvFx.eLk/LI20CLQxGHPK8xC8AR6tlNZ1aSVm';

    public function __construct(
        private readonly AuthRepositoryInterface $authRepository,
        private readonly CustomerService $customerService,
    ) {}

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     * @return array{user: User, token: string}
     */
    public function register(array $attributes): array
    {
        $user = DB::transaction(function () use ($attributes): User {
            $role = $this->authRepository->activeCustomerRole();

            if ($role === null) {
                throw new LogicException('The active customer role has not been configured.');
            }

            $user = $this->authRepository->createUser($attributes, $role);
            $this->customerService->createForUser($user);

            return $user;
        });

        $token = $this->authRepository->createToken($user, 'api')->plainTextToken;

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            report($exception);
        }

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * @return array{user: User, token: string}|null
     */
    public function login(string $email, string $password): ?array
    {
        $user = $this->authRepository->findByEmail($email);
        $passwordMatches = Hash::check($password, $user?->password ?? self::DUMMY_PASSWORD_HASH);

        if ($user === null || ! $user->is_active || ! $passwordMatches) {
            return null;
        }

        return [
            'user' => $user,
            'token' => $this->authRepository->createToken($user, 'api')->plainTextToken,
        ];
    }

    public function spaLogin(array $credentials, bool $rememberMe): bool
    {
        if (!Auth::guard('web')->attempt($credentials, $rememberMe)) {
            return false;
        }
        request()->session()->regenerate();
        return true;
    }

    public function logout(User $user): void
    {
        $this->authRepository->revokeCurrentToken($user);
    }

    public function sendPasswordResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * @param  array{email: string, token: string, password: string}  $credentials
     */
    public function resetPassword(array $credentials): string
    {
        return Password::reset($credentials, function (User $user, string $password): void {
            $this->authRepository->updatePasswordAndRevokeTokens($user, $password);
            event(new PasswordReset($user));
        });
    }

    public function changePassword(User $user, string $password): void
    {
        $this->authRepository->updatePasswordAndRevokeTokens($user, $password);
    }

    public function verifyEmail(int $id, string $hash): ?User
    {
        $user = $this->authRepository->findById($id);

        if ($user === null || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return null;
        }

        if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $user;
    }

    public function resendVerificationEmail(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        return true;
    }
}
