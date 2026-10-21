<?php

namespace App\Contracts;

use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

interface AuthRepositoryInterface
{
    public function activeCustomerRole(): ?Role;

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function createUser(array $attributes, Role $role): User;

    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function createToken(User $user, string $name): NewAccessToken;

    public function revokeCurrentToken(User $user): void;

    public function updatePasswordAndRevokeTokens(User $user, string $password): void;
}
