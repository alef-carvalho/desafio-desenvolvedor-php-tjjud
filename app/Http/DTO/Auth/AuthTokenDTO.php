<?php

namespace App\Http\DTO\Auth;

class AuthTokenDTO
{
    public function __construct(
        public string $access_token,
        public string $token_type
    ) {
    }
}
