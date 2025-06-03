<?php

namespace App\Http\DTO\Book;

class CreateBookDTO
{
    public function __construct(
        public array $authors,
        public string $title,
        public int $edition,
        public string $publisher,
        public string $published_year,
        public array $subjects,
    ) {
    }
}
