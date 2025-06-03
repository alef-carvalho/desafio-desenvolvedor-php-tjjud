<?php

namespace App\Service\Interface;

use App\Http\DTO\Book\BookDTO;
use App\Http\DTO\Book\CreateBookDTO;
use App\Http\DTO\Book\UpdateBookDTO;
use App\Http\DTO\Pagination\PaginationDTO;

interface IBookService
{
    public function findOne(int $id): BookDTO;
    public function findAll(int $page = 1, int $size = 10): PaginationDTO;
    public function create(CreateBookDTO $data): BookDTO;
    public function update(int $id, UpdateBookDTO $data): void;
    public function delete(int $id): void;
}
