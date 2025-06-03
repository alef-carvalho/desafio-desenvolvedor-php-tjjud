<?php

namespace App\Service\Interface;

use App\Http\DTO\Pagination\PaginationDTO;
use App\Http\DTO\Subject\CreateSubjectDTO;
use App\Http\DTO\Subject\SubjectDTO;
use App\Http\DTO\Subject\UpdateSubjectDTO;

interface ISubjectService
{
    public function findOne(int $id): SubjectDTO;
    public function findAll(int $page = 1, int $size = 10): PaginationDTO;
    public function create(CreateSubjectDTO $data): SubjectDTO;
    public function update(int $id, UpdateSubjectDTO $data): void;
    public function delete(int $id): void;
}
