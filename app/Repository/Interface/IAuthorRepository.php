<?php

namespace App\Repository\Interface;

use App\Models\Author;
use Prettus\Repository\Contracts\RepositoryInterface;

interface IAuthorRepository extends RepositoryInterface
{
    public function findByName(string $name): ?Author;
}
