<?php

namespace App\Service;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DatabaseConnectionException;
use App\Exceptions\DatabaseQueryException;
use App\Factory\AuthorFactory;
use App\Http\DTO\Author\AuthorDTO;
use App\Http\DTO\Author\CreateAuthorDTO;
use App\Http\DTO\Author\UpdateAuthorDTO;
use App\Http\DTO\Pagination\PaginationDTO;
use App\Http\DTO\Pagination\PaginationMetadataDTO;
use App\Repository\Interface\IAuthorRepository;
use App\Service\Interface\IAuthorService;
use App\Util\Cache\CacheUtil;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDOException;
use Throwable;

class AuthorService implements IAuthorService
{
    public function __construct(private readonly IAuthorRepository $repository)
    {
    }

    /**
     * @throws NotFoundException
     */
    public function findOne(int $id): AuthorDTO
    {
        $author = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$author) {
            throw new NotFoundException("Autor não encontrado");
        }

        return AuthorFactory::fromModel($author);
    }

    public function findAll(int $page = 1, int $size = 10): PaginationDTO
    {
        $cacheKey = CacheUtil::key("authors.paginated", [$page, $size]);

        $paginator = Cache::remember($cacheKey, CacheUtil::SHORT, function () use ($page, $size) {
            return $this->repository->findPaginated(page: $page, perPage: $size, orderBy: "name");
        });

        $items = collect($paginator->items())
            ->map(fn ($author) => AuthorFactory::fromModel($author))
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
    public function create(CreateAuthorDTO $data): AuthorDTO
    {
        $exists = $this->repository->findByName($data->name);

        if ($exists) {
            throw new ConflictException("Autor ja cadastrado");
        }

        try {
            $author = DB::transaction(function () use ($data) {
                return $this->repository->create([
                    "name" => $data->name
                ]);
            });
        } catch (QueryException $e) {
            throw new DatabaseQueryException(previous: $e);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(previous: $e);
        }

        return AuthorFactory::fromModel($author);

    }

    /**
     * @throws ConflictException
     * @throws DatabaseQueryException
     * @throws DatabaseConnectionException|Throwable
     */
    public function update(int $id, UpdateAuthorDTO $data): void
    {

        $author = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$author) {
            throw new NotFoundException("Autor nao encontrado");
        }

        $exists = $this->repository->findByName($data->name);

        if ($exists && $exists->id !== $id) {
            throw new ConflictException("Autor \"$data->name\" ja cadastrado");
        }

        try {
            DB::transaction(function () use ($id, $data) {
                return $this->repository->update(["name" => $data->name], $id);
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

        $author = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$author) {
            throw new NotFoundException("Autor nao encontrado");
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
