<?php

namespace App\Http\Controllers\API;

use App\Factory\AuthorFactory;
use App\Traits\JsonController;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Author\StoreAuthorFormRequest;
use App\Http\Requests\API\Author\UpdateAuthorFormRequest;
use App\Service\Interface\IAuthorService;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Where;

#[Prefix('api/v1/authors')]
#[Where('id', '[0-9]+')]
#[Middleware("auth:sanctum")]
class AuthorController extends Controller
{
    use JsonController;

    public function __construct(private readonly IAuthorService $service)
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
        $author = $this->service->findOne($id);
        return $this->success($author);
    }

    #[Post('')]
    public function create(StoreAuthorFormRequest $request): JsonResponse
    {
        $data = AuthorFactory::fromStoreRequest($request);
        $author = $this->service->create($data);
        return $this->success($author);
    }

    #[Put('/{id}')]
    public function update(int $id, UpdateAuthorFormRequest $request): JsonResponse
    {
        $data = AuthorFactory::fromUpdateRequest($request);
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
