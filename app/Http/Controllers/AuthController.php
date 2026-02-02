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
        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json($this->toSessionDto($user, $token), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
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
