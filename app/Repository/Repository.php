<?php

namespace App\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

abstract class Repository extends BaseRepository
{
    /**
     * @throws RepositoryException
     */
    public function findPaginated(int $page = 1, int $perPage = 10, array $columns = ['*'], string $orderBy = 'id', string $order = 'asc'): LengthAwarePaginator
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->newQuery()
            ->orderBy($orderBy, $order)
            ->paginate(perPage: $perPage, columns: $columns, page: $page);

        $this->resetScope();
        $this->resetCriteria();
        $this->resetModel();

        return $this->parserResult($results);
    }
}
