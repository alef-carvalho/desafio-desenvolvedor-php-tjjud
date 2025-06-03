<?php

namespace App\Factory;

use App\Http\DTO\Book\BookDTO;
use App\Http\DTO\Book\CreateBookDTO;
use App\Http\DTO\Book\UpdateBookDTO;
use App\Http\Requests\API\Book\StoreBookFormRequest;
use App\Http\Requests\API\Book\UpdateBookFormRequest;
use App\Models\Book;

class BookFactory
{
    public static function fromStoreRequest(StoreBookFormRequest $request): CreateBookDTO
    {
        return new CreateBookDTO(
            authors: $request->array("authors"),
            title: $request->input("title"),
            edition: $request->input("edition"),
            publisher: $request->input("publisher"),
            published_year: $request->input("published_year"),
            subjects: $request->array("subjects"),
        );
    }

    public static function fromUpdateRequest(UpdateBookFormRequest $request): UpdateBookDTO
    {
        return new UpdateBookDTO(
            authors: $request->array("authors"),
            title: $request->input("title"),
            edition: $request->input("edition"),
            publisher: $request->input("publisher"),
            published_year: $request->input("published_year"),
            subjects: $request->array("subjects"),
        );
    }

    public static function fromModel(Book $book): BookDTO
    {
        $authors = $book->authors->map(fn ($author) => AuthorFactory::fromModel($author))
            ->toArray();
        $subjects = $book->subjects->map(fn ($subject) => SubjectFactory::fromModel($subject))
            ->toArray();

        return new BookDTO(
            id: $book->id,
            title: $book->title,
            authors: $authors,
            edition: $book->edition,
            publisher: $book->publisher,
            published_year: $book->published_year,
            subjects: $subjects,
            created_at: $book->created_at->toIso8601String(),
            updated_at: $book->updated_at->toIso8601String()
        );
    }
}
