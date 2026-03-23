<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PhoneLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(PhoneLoginRequest $request): JsonResponse
    {
        /** @var User|null $user */
        $user = User::query()
            ->where('phone', $request->string('phone'))
            ->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            return response()->json([
                'message' => 'Invalid phone or password.',
            ], 422);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'This account is inactive.',
            ], 403);
        }

        $token = $user->createToken($request->input('device_name', 'android-app'))->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
                'account_code' => $user->account_code,
                'is_active' => $user->is_active,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
                'account_code' => $user->account_code,
                'is_active' => $user->is_active,
            ],
        ]);
    }
}
