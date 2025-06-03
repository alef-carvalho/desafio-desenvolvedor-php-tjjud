<?php

namespace Database\Seeders;

use App\Repository\Interface\IAuthorRepository;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AuthorSeeder extends Seeder
{
    public function __construct(private readonly IAuthorRepository $repository)
    {
    }

    protected array $authors = [
        ['id' => 1, 'name' => 'George Orwell'],
        ['id' => 2, 'name' => 'Jane Austen'],
        ['id' => 3, 'name' => 'J.K. Rowling'],
        ['id' => 4, 'name' => 'Stephen King'],
        ['id' => 5, 'name' => 'Isaac Asimov'],
        ['id' => 6, 'name' => 'Agatha Christie'],
        ['id' => 7, 'name' => 'J.R.R. Tolkien'],
        ['id' => 8, 'name' => 'Ernest Hemingway'],
        ['id' => 9, 'name' => 'Mark Twain'],
        ['id' => 10, 'name' => 'F. Scott Fitzgerald'],
    ];

    public function run(): void
    {
        foreach ($this->authors as $author) {
            $this->repository->updateOrCreate(['id' => $author['id']], $author);
        }
    }
}
