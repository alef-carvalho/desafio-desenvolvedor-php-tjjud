<?php

namespace Tests\Unit\Service;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Http\DTO\Author\AuthorDTO;
use App\Http\DTO\Author\CreateAuthorDTO;
use App\Http\DTO\Author\UpdateAuthorDTO;
use App\Models\Author;
use App\Repository\AuthorRepository;
use App\Service\AuthorService;
use App\Util\Cache\CacheUtil;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AuthorServiceTest extends TestCase
{

    /**
     * @var AuthorRepository&MockInterface
     */
    protected $repository;
    protected AuthorService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(AuthorRepository::class);
        $this->service = new AuthorService($this->repository);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn ($callback) => $callback());
    }

    public function testCreateAuthorSuccessfully()
    {
        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';
        $author->created_at = Carbon::now();
        $author->updated_at = Carbon::now();

        $this->repository
            ->shouldReceive('findByName')
            ->once()
            ->with($author->name)
            ->andReturn(null);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::any())
            ->andReturn($author);

        $dto = new CreateAuthorDTO(name: $author->name);
        $result = $this->service->create($dto);

        $this->assertInstanceOf(AuthorDTO::class, $result);
        $this->assertEquals($author->id, $result->id);
        $this->assertEquals($author->name, $result->name);
    }

    public function testCreateThrowsConflictIfNameExists()
    {
        $this->expectException(ConflictException::class);

        $author = new Author();
        $author->name = 'Ernest Hemingway';

        $createAuthorDTO = new CreateAuthorDTO(name: $author->name);

        $this->repository
            ->shouldReceive('findByName')
            ->once()
            ->with($author->name)
            ->andReturn($author);

        $this->service->create($createAuthorDTO);
    }

    public function testUpdateAuthorSuccessfully()
    {
        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';
        $author->created_at = Carbon::now();
        $author->updated_at = Carbon::now();

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $author->id])
            ->andReturn(collect([$author]));

        $this->repository
            ->shouldReceive('findByName')
            ->once()
            ->with($author->name)
            ->andReturn(null);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with(['name' => $author->name], $author->id);


        $dto = new UpdateAuthorDTO(name: $author->name);
        $this->service->update($author->id, $dto);

        $this->assertTrue(true);
    }

    public function testUpdateThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';

        $dto = new UpdateAuthorDTO(name: $author->name);
        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $author->id])
            ->andReturn(collect());

        $this->service->update($author->id, $dto);
    }

    public function testUpdateThrowsConflictWhenNameExistsOnAnotherAuthor()
    {
        $this->expectException(ConflictException::class);

        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';

        $secondAuthor = new Author();
        $secondAuthor->id = 2;
        $secondAuthor->name = 'George Orwell';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $author->id])
            ->andReturn(collect([$author]));

        $this->repository
            ->shouldReceive('findByName')
            ->with($secondAuthor->name)
            ->andReturn($secondAuthor);

        $dto = new UpdateAuthorDTO(name: $secondAuthor->name);

        $this->service->update($author->id, $dto);
    }

    public function testDeleteAuthorSuccessfully()
    {
        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $author->id])
            ->andReturn(collect([$author]));

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($author->id);

        $this->service->delete($author->id);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $author->id])
            ->andReturn(collect());

        $this->service->delete($author->id);
    }

    public function testFindOneSuccessfully()
    {
        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';
        $author->created_at = Carbon::now();
        $author->updated_at = Carbon::now();

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $author->id])
            ->andReturn(collect([$author]));

        $result = $this->service->findOne($author->id);

        $this->assertInstanceOf(AuthorDTO::class, $result);
        $this->assertEquals($author->id, $result->id);
        $this->assertEquals($author->name, $result->name);
    }

    public function testFindOneThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $author->id])
            ->andReturn(collect());

        $this->service->findOne($author->id);
    }

    public function testFindPaginated()
    {
        $author = new Author();
        $author->id = 1;
        $author->name = 'Ernest Hemingway';
        $author->created_at = Carbon::now();
        $author->updated_at = Carbon::now();

        $page = 1;
        $size = 5;
        $total = 1;
        $paginator = new LengthAwarePaginator([$author], $total, $size, $page);

        $cacheKey = CacheUtil::key("authors.paginated", [$page, $size]);

        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, CacheUtil::SHORT, \Closure::class)
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->repository
            ->shouldReceive('findPaginated')
            ->with($page, $size, ["*"], "name")
            ->andReturn($paginator);

        $response = $this->service->findAll($page, $size);

        $this->assertEquals($size, $response->meta->size);
        $this->assertEquals($page, $response->meta->totalPages);
        $this->assertEquals($total, $response->meta->totalResults);

        $this->assertEquals($author->id, $response->data[0]->id);
        $this->assertEquals($author->name, $response->data[0]->name);

    }

}
