<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->register($request->safe()->only(['name', 'email', 'password']));

        return response()->json([
            'message' => 'Registration successful. Please verify your email address.',
            'data' => $this->authData($result['user'], $result['token'], $request),
        ], 201);
    }

    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->login($request->validated('email'), $request->validated('password'));

        if ($result === null) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful.',
            'data' => $this->authData($result['user'], $result['token'], $request),
        ]);
    }

    public function spaLogin(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $credentials = $request->safe()->only([ 'email', 'password', ]);
        $rememberMe = $request->boolean('remember_me');

        if (! $authService->spaLogin($credentials, $rememberMe)) {
            return response()->json([
                'email' => 'Invalid credentials.',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful.',
            'data' => $this->authUser($request),
        ], 200);
    }

    public function logout(Request $request, AuthService $authService): JsonResponse
    {
        $authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    public function spaLogout(Request $request): JsonResponse 
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successfully.',
        ], 200);
    }

    public function forgotPassword(
        ForgotPasswordRequest $request,
        AuthService $authService,
    ): JsonResponse {
        $authService->sendPasswordResetLink($request->validated('email'));

        return response()->json([
            'message' => 'If an account exists for that email address, a password reset link has been sent.',
        ]);
    }

    public function resetPassword(
        ResetPasswordRequest $request,
        AuthService $authService,
    ): JsonResponse {
        $status = $authService->resetPassword($request->safe()->only([
            'email',
            'token',
            'password',
        ]));

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successful.',
            ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Please wait before trying again.',
            ], 429);
        }

        return response()->json([
            'message' => 'The password reset token is invalid or expired.',
            'errors' => [
                'token' => ['The password reset token is invalid or expired.'],
            ],
        ], 422);
    }

    public function changePassword(
        ChangePasswordRequest $request,
        AuthService $authService,
    ): JsonResponse {
        $authService->changePassword($request->user(), $request->validated('password'));

        return response()->json([
            'message' => 'Password changed successfully. Please sign in again.',
        ]);
    }

    public function verifyEmail(
        Request $request,
        int $id,
        string $hash,
        AuthService $authService,
    ): JsonResponse {
        $user = $authService->verifyEmail($id, $hash);

        if ($user === null) {
            return response()->json([
                'message' => 'The email verification link is invalid.',
            ], 403);
        }

        return response()->json([
            'message' => 'Email verified successfully.',
            'data' => UserResource::make($user)->resolve($request),
        ]);
    }

    public function resendVerificationEmail(Request $request, AuthService $authService): JsonResponse
    {
        $sent = $authService->resendVerificationEmail($request->user());

        return response()->json([
            'message' => $sent
                ? 'A verification email has been sent.'
                : 'Your email address is already verified.',
        ], $sent ? 202 : 200);
    }

    /**
     * @return array{user: array<string, mixed>, token: string}
     */
    private function authData(User $user, string $token, Request $request): array
    {
        return [
            'user' => UserResource::make($user)->resolve($request),
            'token' => $token,
        ];
    }

    private function authUser(Request $request): array
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        
        return UserResource::make($user->load('role'))->resolve($request);
    }
}
