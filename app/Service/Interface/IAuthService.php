<?php

namespace App\Service\Interface;

use App\Http\DTO\Auth\AuthTokenDTO;
use Illuminate\Http\Request;

interface IAuthService
{
    public function login(array $credentials): AuthTokenDTO;
    public function logout(Request $request): void;
}
