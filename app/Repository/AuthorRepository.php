<?php

namespace App\Repository;

use App\Models\Author;
use App\Repository\Interface\IAuthorRepository;

class AuthorRepository extends Repository implements IAuthorRepository
{
    public function model(): string
    {
        return Author::class;
    }

    public function findByName(string $name): ?Author
    {
        return $this->model
            ->newQuery()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();
    }
}
