<?php

namespace App\Http\Controllers\Api;

use App\Domain\User\Contracts\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Support\ApiResponse;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(protected UserServiceInterface $userService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->register($request->validated());
        event(new Registered($user));

        $token = $user->createToken('api', ['profile.manage'])->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful', [], [], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->userService->login($request->validated());

        if (! $user) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        $token = $user->createToken('api', ['profile.manage'])->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->validated());

        if ($status != Password::RESET_LINK_SENT) {
            return ApiResponse::error(trans($status), 500);
        }

        return ApiResponse::success(null, 'Password reset link sent successfully.');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->validated(),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            return ApiResponse::error(trans($status), 500);
        }

        return ApiResponse::success(null, 'Password reset successful.');
    }

    public function sendEmailVerificationNotification(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();

        return ApiResponse::success(null, 'Verification email sent successfully.');
    }

    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        $user = $this->userService->findById((int) $id);

        if (! $user || ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return ApiResponse::error('Invalid verification link.', 403);
        }

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified.');
        }

        $user->markEmailAsVerified();

        return ApiResponse::success(null, 'Email verified successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()), 'Current user retrieved successfully');
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile($request->user(), $request->validated());

        return ApiResponse::success(new UserResource($user), 'Profile updated successfully');
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->userService->updatePassword($request->user(), $request->validated());

        return ApiResponse::success(null, 'Password updated successfully.');
    }

    public function deleteToken(Request $request, string $id): JsonResponse
    {
        $this->userService->revokeToken($request->user(), $id);

        return ApiResponse::success(null, 'Token revoked successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->userService->logout($request->user());

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
