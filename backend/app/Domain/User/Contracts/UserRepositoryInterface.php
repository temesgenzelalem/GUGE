<?php

namespace App\Domain\User\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function query(array $filters = []): Builder;

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;
}
