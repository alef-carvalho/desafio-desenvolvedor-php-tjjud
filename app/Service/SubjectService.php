<?php

namespace App\Service;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DatabaseConnectionException;
use App\Exceptions\DatabaseQueryException;
use App\Factory\SubjectFactory;
use App\Http\DTO\Book\CreateBookDTO;
use App\Http\DTO\Book\UpdateBookDTO;
use App\Http\DTO\Pagination\PaginationDTO;
use App\Http\DTO\Pagination\PaginationMetadataDTO;
use App\Http\DTO\Subject\CreateSubjectDTO;
use App\Http\DTO\Subject\SubjectDTO;
use App\Http\DTO\Subject\UpdateSubjectDTO;
use App\Repository\Interface\ISubjectRepository;
use App\Service\Interface\ISubjectService;
use App\Util\Cache\CacheUtil;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDOException;
use Throwable;

class SubjectService implements ISubjectService
{
    public function __construct(private readonly ISubjectRepository $repository)
    {
    }

    /**
     * @throws NotFoundException
     */
    public function findOne(int $id): SubjectDTO
    {
        $subject = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$subject) {
            throw new NotFoundException("Assunto não encontrado");
        }

        return SubjectFactory::fromModel($subject);
    }

    public function findAll(int $page = 1, int $size = 10): PaginationDTO
    {
        $cacheKey = CacheUtil::key("subjects.paginated", [$page, $size]);

        $paginator = Cache::remember($cacheKey, CacheUtil::SHORT, function () use ($page, $size) {
            return $this->repository->findPaginated(
                page: $page,
                perPage: $size,
                orderBy: "description"
            );
        });

        $items = collect($paginator->items())
            ->map(fn ($subject) => SubjectFactory::fromModel($subject))
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
    public function create(CreateSubjectDTO $data): SubjectDTO
    {
        $exists = $this->repository->findByName($data->description);

        if ($exists) {
            throw new ConflictException("Assunto ja cadastrado");
        }

        try {
            $result = DB::transaction(function () use ($data) {
                return $this->repository->create(["description" => $data->description]);
            });
        } catch (QueryException $e) {
            throw new DatabaseQueryException(previous: $e);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(previous: $e);
        }

        return SubjectFactory::fromModel($result);

    }

    /**
     * @throws ConflictException
     * @throws DatabaseQueryException
     * @throws DatabaseConnectionException|Throwable
     */
    public function update(int $id, UpdateSubjectDTO $data): void
    {

        $subject = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$subject) {
            throw new NotFoundException("Assunto nao encontrado");
        }

        $exists = $this->repository->findByName($data->description);

        if ($exists && $exists->id !== $id) {
            throw new ConflictException("Assunto \"$data->description\" ja cadastrado");
        }

        try {
            DB::transaction(function () use ($id, $data) {
                $this->repository->update(["description" => $data->description], $id);
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

        $subject = $this->repository
            ->findWhere(["id" => $id])
            ->first();

        if (!$subject) {
            throw new NotFoundException("Assunto nao encontrado");
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
