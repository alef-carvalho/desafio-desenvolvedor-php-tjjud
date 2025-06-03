<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Repository\Interface\IBookRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BookSeeder extends Seeder
{
    protected array $books = [
        [
            'id' => 1,
            'author_id' => 1,
            'edition' => 1,
            'title' => '1984',
            'publisher' => 'Secker & Warburg',
            'published_year' => '1949',
        ],
        [
            'id' => 2,
            'author_id' => 2,
            'title' => 'Orgulho e Preconceito',
            'edition' => 1,
            'publisher' => 'T. Egerton',
            'published_year' => '1813',
        ],
        [
            'id' => 3,
            'author_id' => 3,
            'edition' => 1,
            'title' => 'Harry Potter e a Pedra Filosofal',
            'publisher' => 'Bloomsbury',
            'published_year' => '1997',
        ],
        [
            'id' => 4,
            'author_id' => 4,
            'title' => 'O Iluminado',
            'edition' => 1,
            'publisher' => 'Doubleday',
            'published_year' => '1977',
        ],
        [
            'id' => 5,
            'author_id' => 5,
            'title' => 'Fundação',
            'edition' => 1,
            'publisher' => 'Gnome Press',
            'published_year' => '1951',
        ],
        [
            'id' => 6,
            'author_id' => 6,
            'title' => 'Assassinato no Expresso do Oriente',
            'edition' => 1,
            'publisher' => 'Collins Crime Club',
            'published_year' => '1934',
        ],
        [
            'id' => 7,
            'title' => 'O Hobbit',
            'author_id' => 7,
            'edition' => 1,
            'publisher' => 'Allen & Unwin',
            'published_year' => '1937',
        ],
        [
            'id' => 8,
            'author_id' => 8,
            'title' => 'O Velho e o Mar',
            'edition' => 1,
            'publisher' => 'Charles Scribner\'s Sons',
            'published_year' => '1952',
        ],
        [
            'id' => 9,
            'author_id' => 9,
            'title' => 'As Aventuras de Huckleberry Finn',
            'edition' => 1,
            'publisher' => 'Chatto & Windus',
            'published_year' => '1884',
        ],
        [
            'id' => 10,
            'author_id' => 10,
            'title' => 'O Grande Gatsby',
            'edition' => 1,
            'publisher' => 'Scribner',
            'published_year' => '1925',
        ],
    ];

    public function __construct(private readonly IBookRepository $repository)
    {
    }

    public function run(): void
    {
        foreach ($this->books as $item) {

            $book = $this->repository->updateOrCreate(['id' => $item['id']], Arr::except($item, ['author_id']));

            $book->authors()->sync([
                $item['author_id']
            ]);

            // associa 1 ou 2 assuntos aleatórios para o livro
            $subjects = Subject::query()
                ->inRandomOrder()
                ->limit(rand(1, 2))
                ->pluck('id');

            $book->subjects()->sync($subjects);

        }
    }
}
