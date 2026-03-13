<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user
            ->createToken($credentials['device_name'] ?? 'react-dashboard')
            ->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Revoke only the token used for this request.
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        $token?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }

    public function user(): JsonResponse
    {
        return response()->json([
            'user' => Auth::user(),
        ], 200);
    }
}