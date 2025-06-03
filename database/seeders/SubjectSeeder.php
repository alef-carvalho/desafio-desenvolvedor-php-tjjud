<?php

namespace Database\Seeders;

use App\Repository\Interface\ISubjectRepository;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    protected array $subjects = [
        ['id' => 1, 'description' => 'Ficção Científica'],
        ['id' => 2, 'description' => 'Mistério'],
        ['id' => 3, 'description' => 'Fantasia'],
        ['id' => 4, 'description' => 'Romance'],
        ['id' => 5, 'description' => 'Clássico'],
        ['id' => 6, 'description' => 'Aventura'],
        ['id' => 7, 'description' => 'Drama'],
        ['id' => 8, 'description' => 'Biografia'],
        ['id' => 9, 'description' => 'Suspense'],
        ['id' => 10, 'description' => 'Terror'],
    ];

    public function __construct(private readonly ISubjectRepository $repository)
    {
    }

    public function run(): void
    {
        foreach ($this->subjects as $subject) {
            $this->repository->updateOrCreate(['id' => $subject['id']], $subject);
        }
    }
}
