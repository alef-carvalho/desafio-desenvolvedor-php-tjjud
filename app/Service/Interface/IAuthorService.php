<?php

namespace App\Service\Interface;

use App\Http\DTO\Author\AuthorDTO;
use App\Http\DTO\Author\CreateAuthorDTO;
use App\Http\DTO\Author\UpdateAuthorDTO;
use App\Http\DTO\Pagination\PaginationDTO;

interface IAuthorService
{
    public function findOne(int $id): AuthorDTO;
    public function findAll(int $page = 1, int $size = 10): PaginationDTO;
    public function create(CreateAuthorDTO $data): AuthorDTO;
    public function update(int $id, UpdateAuthorDTO $data): void;
    public function delete(int $id): void;
}
