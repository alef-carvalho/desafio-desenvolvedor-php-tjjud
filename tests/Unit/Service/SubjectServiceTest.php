<?php

namespace Service;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Http\DTO\Subject\SubjectDTO;
use App\Http\DTO\Subject\CreateSubjectDTO;
use App\Http\DTO\Subject\UpdateSubjectDTO;
use App\Models\Subject;
use App\Repository\SubjectRepository;
use App\Service\SubjectService;
use App\Util\Cache\CacheUtil;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class SubjectServiceTest extends TestCase
{

    /**
     * @var SubjectRepository&MockInterface
     */
    protected $repository;
    protected SubjectService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(SubjectRepository::class);
        $this->service = new SubjectService($this->repository);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn ($callback) => $callback());
    }

    public function testCreateSubjectSuccessfully()
    {
        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';
        $subject->created_at = Carbon::now();
        $subject->updated_at = Carbon::now();

        $this->repository
            ->shouldReceive('findByName')
            ->once()
            ->with($subject->description)
            ->andReturn(null);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::any())
            ->andReturn($subject);

        $dto = new CreateSubjectDTO(description: $subject->description);
        $result = $this->service->create($dto);

        $this->assertInstanceOf(SubjectDTO::class, $result);
        $this->assertEquals($subject->id, $result->id);
        $this->assertEquals($subject->description, $result->description);
    }

    public function testCreateThrowsConflictIfNameExists()
    {
        $this->expectException(ConflictException::class);

        $subject = new Subject();
        $subject->description = 'Ficção Científica';

        $createSubjectDTO = new CreateSubjectDTO(description: $subject->description);

        $this->repository
            ->shouldReceive('findByName')
            ->once()
            ->with($subject->description)
            ->andReturn($subject);

        $this->service->create($createSubjectDTO);
    }

    public function testUpdateSubjectSuccessfully()
    {
        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';
        $subject->created_at = Carbon::now();
        $subject->updated_at = Carbon::now();

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $subject->id])
            ->andReturn(collect([$subject]));

        $this->repository
            ->shouldReceive('findByName')
            ->once()
            ->with($subject->description)
            ->andReturn(null);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with(['description' => $subject->description], $subject->id);


        $dto = new UpdateSubjectDTO(description: $subject->description);
        $this->service->update($subject->id, $dto);

        $this->assertTrue(true);
    }

    public function testUpdateThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';

        $dto = new UpdateSubjectDTO(description: $subject->description);
        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $subject->id])
            ->andReturn(collect());

        $this->service->update($subject->id, $dto);
    }

    public function testUpdateThrowsConflictWhenNameExistsOnAnotherSubject()
    {
        $this->expectException(ConflictException::class);

        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';

        $secondSubject = new Subject();
        $secondSubject->id = 2;
        $secondSubject->description = 'Biografia';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $subject->id])
            ->andReturn(collect([$subject]));

        $this->repository
            ->shouldReceive('findByName')
            ->with($secondSubject->description)
            ->andReturn($secondSubject);

        $dto = new UpdateSubjectDTO(description: $secondSubject->description);

        $this->service->update($subject->id, $dto);
    }

    public function testDeleteSubjectSuccessfully()
    {
        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $subject->id])
            ->andReturn(collect([$subject]));

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($subject->id);

        $this->service->delete($subject->id);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $subject->id])
            ->andReturn(collect());

        $this->service->delete($subject->id);
    }

    public function testFindOneSuccessfully()
    {
        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';
        $subject->created_at = Carbon::now();
        $subject->updated_at = Carbon::now();

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $subject->id])
            ->andReturn(collect([$subject]));

        $result = $this->service->findOne($subject->id);

        $this->assertInstanceOf(SubjectDTO::class, $result);
        $this->assertEquals($subject->id, $result->id);
        $this->assertEquals($subject->description, $result->description);
    }

    public function testFindOneThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $subject->id])
            ->andReturn(collect());

        $this->service->findOne($subject->id);
    }

    public function testFindPaginated()
    {
        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Ficção Científica';
        $subject->created_at = Carbon::now();
        $subject->updated_at = Carbon::now();

        $page = 1;
        $size = 5;
        $total = 1;
        $paginator = new LengthAwarePaginator([$subject], $total, $size, $page);

        $cacheKey = CacheUtil::key("subjects.paginated", [$page, $size]);

        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, CacheUtil::SHORT, \Closure::class)
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->repository
            ->shouldReceive('findPaginated')
            ->with($page, $size, ["*"], "description")
            ->andReturn($paginator);

        $response = $this->service->findAll($page, $size);

        $this->assertEquals($size, $response->meta->size);
        $this->assertEquals($page, $response->meta->totalPages);
        $this->assertEquals($total, $response->meta->totalResults);

        $this->assertEquals($subject->id, $response->data[0]->id);
        $this->assertEquals($subject->description, $response->data[0]->description);

    }

}
