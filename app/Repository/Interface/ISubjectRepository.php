<?php

namespace App\Repository\Interface;

use App\Models\Subject;
use Prettus\Repository\Contracts\RepositoryInterface;

interface ISubjectRepository extends RepositoryInterface
{
    public function findByName(string $name): ?Subject;

}
