<?php

namespace App\Service;

use Illuminate\Http\Request;
use App\Http\DTO\Auth\AuthTokenDTO;
use App\Service\Interface\IAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class AuthService implements IAuthService
{
    public function login(array $credentials): AuthTokenDTO
    {
        if (!Auth::attempt($credentials)) {
            throw new UnauthorizedException("Credenciais inválidas");
        }

        $user = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return new AuthTokenDTO(access_token: $token, token_type: 'Bearer');
    }

    public function logout(Request $request): void
    {
        $request->user()->tokens()->delete();
    }
}
