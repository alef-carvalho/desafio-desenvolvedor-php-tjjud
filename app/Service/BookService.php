<?php

namespace App\Service;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DatabaseConnectionException;
use App\Exceptions\DatabaseQueryException;
use App\Factory\BookFactory;
use App\Http\DTO\Book\BookDTO;
use App\Http\DTO\Book\CreateBookDTO;
use App\Http\DTO\Book\UpdateBookDTO;
use App\Http\DTO\Pagination\PaginationDTO;
use App\Http\DTO\Pagination\PaginationMetadataDTO;
use App\Repository\Interface\IBookRepository;
use App\Service\Interface\IBookService;
use App\Util\Cache\CacheUtil;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDOException;
use Throwable;

class BookService implements IBookService
{
    public function __construct(private readonly IBookRepository $repository)
    {
    }

    /**
     * @throws NotFoundException
     */
    public function findOne(int $id): BookDTO
    {
        $book = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$book) {
            throw new NotFoundException("Livro não encontrado");
        }

        return BookFactory::fromModel($book);
    }

    public function findAll(int $page = 1, int $size = 10): PaginationDTO
    {
        $cacheKey = CacheUtil::key("books.paginated", [$page, $size]);

        $paginator = Cache::remember($cacheKey, CacheUtil::SHORT, function () use ($page, $size) {
            return $this->repository->findPaginated(page: $page, perPage: $size, orderBy: "title");
        });

        $items = collect($paginator->items())
            ->map(fn ($book) => BookFactory::fromModel($book))
            ->all();

        return new PaginationDTO($items, new PaginationMetadataDTO(
            $page,
            $size,
            $paginator->lastPage(),
            $paginator->total()
        ));
    }

    /**
     * @throws ConflictException
     * @throws DatabaseQueryException
     * @throws DatabaseConnectionException|Throwable
     */
    public function create(CreateBookDTO $data): BookDTO
    {
        $exists = $this->repository->findByTitle($data->title);

        if ($exists) {
            throw new ConflictException("Livro ja cadastrado");
        }

        try {
            $result = DB::transaction(function () use ($data) {
                $book = $this->repository->create([
                    "title"          => $data->title,
                    "edition"        => $data->edition,
                    "publisher"      => $data->publisher,
                    "published_year" => $data->published_year
                ]);
                $book->authors()->sync($data->authors);
                $book->subjects()->sync($data->subjects);
                return $book;
            });
        } catch (QueryException $e) {
            throw new DatabaseQueryException(previous: $e);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(previous: $e);
        }

        return BookFactory::fromModel($result);

    }

    /**
     * @throws ConflictException
     * @throws DatabaseQueryException
     * @throws DatabaseConnectionException|Throwable
     */
    public function update(int $id, UpdateBookDTO $data): void
    {

        $book = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$book) {
            throw new NotFoundException("Livro nao encontrado");
        }

        $exists = $this->repository->findByTitle($data->title);

        if ($exists && $exists->id !== $id) {
            throw new ConflictException("Livro \"$data->title\" ja cadastrado");
        }

        try {
            DB::transaction(function () use ($id, $data) {
                $book = $this->repository->update([
                    "title"          => $data->title,
                    "edition"        => $data->edition,
                    "publisher"      => $data->publisher,
                    "published_year" => $data->published_year
                ], $id);
                $book->authors()->sync($data->authors);
                $book->subjects()->sync($data->subjects);
            });
        } catch (QueryException $e) {
            throw new DatabaseQueryException(previous: $e);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(previous: $e);
        }

    }

    /**
     * @throws ConflictException
     * @throws DatabaseQueryException
     * @throws DatabaseConnectionException|Throwable
     */
    public function delete(int $id): void
    {

        $book = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$book) {
            throw new NotFoundException("Livro nao encontrado");
        }

        try {
            DB::transaction(function () use ($id) {
                $this->repository->delete($id);
            });
        } catch (QueryException $e) {
            throw new DatabaseQueryException(previous: $e);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(previous: $e);
        }

    }
}
