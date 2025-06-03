<?php

namespace App\Http\Controllers\API;

use App\Factory\SubjectFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Subject\StoreSubjectFormRequest;
use App\Http\Requests\API\Subject\UpdateSubjectFormRequest;
use App\Service\Interface\ISubjectService;
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

#[Prefix('api/v1/subjects')]
#[Where('id', '[0-9]+')]
#[Middleware("auth:sanctum")]
class SubjectController extends Controller
{
    use JsonController;

    public function __construct(private readonly ISubjectService $service)
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
    public function create(StoreSubjectFormRequest $request): JsonResponse
    {
        $data = SubjectFactory::fromStoreRequest($request);
        $book = $this->service->create($data);
        return $this->success($book);
    }

    #[Put('/{id}')]
    public function update(int $id, UpdateSubjectFormRequest $request): JsonResponse
    {
        $data = SubjectFactory::fromUpdateRequest($request);
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
