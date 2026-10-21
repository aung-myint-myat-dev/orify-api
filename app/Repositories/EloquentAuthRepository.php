<?php

namespace App\Repositories;

use App\Contracts\AuthRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class EloquentAuthRepository implements AuthRepositoryInterface
{
    public function activeCustomerRole(): ?Role
    {
        return Role::query()
            ->where('name', 'customer')
            ->where('is_active', true)
            ->lockForUpdate()
            ->first();
    }

    public function createUser(array $attributes, Role $role): User
    {
        return User::query()->create([
            ...$attributes,
            'role_id' => $role->getKey(),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function createToken(User $user, string $name): NewAccessToken
    {
        return $user->createToken($name);
    }

    public function revokeCurrentToken(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    public function updatePasswordAndRevokeTokens(User $user, string $password): void
    {
        DB::transaction(function () use ($user, $password): void {
            $user->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60),
            ])->save();

            $user->tokens()->delete();
        });
    }
}
