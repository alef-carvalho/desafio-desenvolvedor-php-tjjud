<?php

namespace App\Repository\Interface;

use App\Models\Book;
use Prettus\Repository\Contracts\RepositoryInterface;

interface IBookRepository extends RepositoryInterface
{
    public function findByTitle(string $name): ?Book;
}
