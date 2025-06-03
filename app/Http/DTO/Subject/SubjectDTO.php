<?php

namespace App\Http\DTO\Subject;

class SubjectDTO
{
    public function __construct(
        public int $id,
        public string $description,
        public string $created_at,
        public string $updated_at,
    ) {
    }
}
