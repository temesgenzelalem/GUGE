<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\User\Contracts\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserServiceInterface $userService)
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(User::class, 'user', ['except' => ['index', 'show']]);
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->listUsers(
            $request->only(['search', 'role']),
            $request->integer('per_page', 20)
        );

        return ApiResponse::success(new UserCollection($users), 'Users retrieved successfully', [
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
            'last_page' => $users->lastPage(),
        ], [
            'next' => $users->nextPageUrl(),
            'prev' => $users->previousPageUrl(),
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return ApiResponse::success(new UserResource($user), 'User created successfully', [], [], 201);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return ApiResponse::success(new UserResource($user), 'User retrieved successfully');
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->updateUser($user, $request->validated());

        return ApiResponse::success(new UserResource($user), 'User updated successfully');
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $this->userService->deleteUser($user);

        return ApiResponse::success(null, 'User deleted successfully');
    }
}
