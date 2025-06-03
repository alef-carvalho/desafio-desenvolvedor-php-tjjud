<?php

namespace App\Repository;

use App\Models\Subject;
use App\Repository\Interface\ISubjectRepository;

class SubjectRepository extends Repository implements ISubjectRepository
{
    public function model(): string
    {
        return Subject::class;
    }

    public function findByName(string $name): ?Subject
    {
        return $this->model
            ->newQuery()
            ->whereRaw('LOWER(description) = ?', [strtolower($name)])
            ->first();
    }
}
