<?php

namespace App\Http\DTO\Book;

class BookDTO
{
    public function __construct(
        public int    $id,
        public string $title,
        public array  $authors,
        public int    $edition,
        public string $publisher,
        public int    $published_year,
        public array  $subjects,
        public string $created_at,
        public string $updated_at,
    ) {
    }
}
