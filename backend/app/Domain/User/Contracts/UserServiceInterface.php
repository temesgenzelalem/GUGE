<?php

namespace App\Domain\User\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function register(array $data): User;

    public function login(array $credentials): ?User;

    public function logout(User $user): void;

    public function listUsers(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function createUser(array $data): User;

    public function findById(int $id): ?User;

    public function updateUser(User $user, array $data): User;

    public function deleteUser(User $user): bool;

    public function updateProfile(User $user, array $data): User;

    public function updatePassword(User $user, array $data): User;

    public function revokeToken(User $user, string $tokenId): void;
}
