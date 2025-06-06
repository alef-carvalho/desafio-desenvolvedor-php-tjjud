<?php

namespace App\Http\DTO\Author;

class AuthorDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $created_at,
        public string $updated_at,
    ) {
    }
}
