<?php

namespace App\Http\Controllers\API;

use App\Factory\BookFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Book\StoreBookFormRequest;
use App\Http\Requests\API\Book\UpdateBookFormRequest;
use App\Service\Interface\IBookService;
use App\Traits\JsonController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Where;

#[Prefix('api/v1/books')]
#[Where('id', '[0-9]+')]
#[Middleware("auth:sanctum")]
class BookController extends Controller
{
    use JsonController;

    public function __construct(private readonly IBookService $service)
    {
        //
    }

    #[Get('/')]
    public function findAll(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $size = $request->query('size', 10);
        $data = $this->service->findAll($page, $size);
        return $this->success($data);
    }

    #[Get('/{id}')]
    public function findOne(int $id): JsonResponse
    {
        $book = $this->service->findOne($id);
        return $this->success($book);
    }

    #[Post('')]
    public function create(StoreBookFormRequest $request): JsonResponse
    {
        $data = BookFactory::fromStoreRequest($request);
        $book = $this->service->create($data);
        return $this->success($book);
    }

    #[Put('/{id}')]
    public function update(int $id, UpdateBookFormRequest $request): JsonResponse
    {
        $data = BookFactory::fromUpdateRequest($request);
        $this->service->update($id, $data);
        return $this->success();
    }

    #[Delete('/{id}')]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);
        return $this->success();
    }
}
