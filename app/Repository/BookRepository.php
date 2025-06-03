<?php

namespace App\Repository;

use App\Models\Book;
use App\Repository\Interface\IBookRepository;

class BookRepository extends Repository implements IBookRepository
{
    public function model(): string
    {
        return Book::class;
    }

    public function findByTitle(string $name): ?Book
    {
        return $this->model
            ->newQuery()
            ->whereRaw('LOWER(title) = ?', [strtolower($name)])
            ->first();
    }
}
