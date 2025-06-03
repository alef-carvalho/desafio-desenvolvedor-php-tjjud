<?php

namespace App\Http\DTO\Subject;

class CreateSubjectDTO
{
    public function __construct(
        public string $description,
    ) {
    }
}
