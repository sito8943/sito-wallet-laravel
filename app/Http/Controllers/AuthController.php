<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function toSessionDto(User $user, string $token): array
    {
        return [
            'id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ];
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->exists()) {
            return response()->json([
                'message' => '422',
                'errors' => ['email' => ['The email has already been taken.']],
            ], 422);
        }

        try {
            $user = User::create([
                'name' => (string) ($data['name'] ?? ''),
                'email' => (string) ($data['email'] ?? ''),
                'password' => Hash::make((string) ($data['password'] ?? '')),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            throw $e;
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json($this->toSessionDto($user, $token), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => '401'], 401);
        }

        $user = $request->user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json($this->toSessionDto($user, $token));
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();
        $user?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(): JsonResponse
    {
        $user = request()->user();
        $token = $user?->currentAccessToken()?->plainTextToken ?? '';
        // If we don't have the plain token, return without token (client already stores it)
        return response()->json([
            'id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ]);
    }
}
