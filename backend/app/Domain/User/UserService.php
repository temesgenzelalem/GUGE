<?php

namespace App\Domain\User;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Contracts\UserServiceInterface;
use App\Domain\User\Events\UserRegistered;
use App\Domain\User\Events\UserUpdated;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService implements UserServiceInterface
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        $user = $this->repository->create($data);

        event(new UserRegistered($user));

        return $user;
    }

    public function login(array $credentials): ?User
    {
        $user = $this->repository->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }

    public function listUsers(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->repository->create($data);

        event(new UserRegistered($user));

        return $user;
    }

    public function findById(int $id): ?User
    {
        return $this->repository->findById($id);
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $updated = $this->repository->update($user, $data);

        event(new UserUpdated($updated));

        return $updated;
    }

    public function deleteUser(User $user): bool
    {
        return $this->repository->delete($user);
    }

    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['email']) && strtolower($data['email']) !== strtolower($user->email)) {
            $user->email_verified_at = null;
        }

        $updated = $this->repository->update($user, $data);

        event(new UserUpdated($updated));

        return $updated;
    }

    public function updatePassword(User $user, array $data): User
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new \InvalidArgumentException('Current password is incorrect.');
        }

        $user->password = Hash::make($data['password']);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return $user;
    }

    public function revokeToken(User $user, string $tokenId): void
    {
        $token = $user->tokens()->find($tokenId);

        if ($token) {
            $token->delete();
        }
    }
}
