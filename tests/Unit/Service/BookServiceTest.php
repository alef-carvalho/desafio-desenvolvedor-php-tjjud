<?php

namespace Service;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Http\DTO\Book\BookDTO;
use App\Http\DTO\Book\CreateBookDTO;
use App\Http\DTO\Book\UpdateBookDTO;
use App\Models\Author;
use App\Models\Book;
use App\Models\Subject;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Util\Cache\CacheUtil;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class BookServiceTest extends TestCase
{

    /**
     * @var BookRepository&MockInterface
     */
    protected $repository;
    protected BookService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(BookRepository::class);
        $this->service = new BookService($this->repository);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn ($callback) => $callback());
    }

    public function testCreateBookSuccessfully()
    {
        $book = Mockery::mock(Book::class)->makePartial();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';
        $book->edition = 1;
        $book->publisher = 'Nelvana';
        $book->authors = new Collection();
        $book->subjects = new Collection();
        $book->published_year = 2004;
        $book->created_at = Carbon::now();
        $book->updated_at = Carbon::now();

        $this->repository
            ->shouldReceive('findByTitle')
            ->once()
            ->with($book->title)
            ->andReturn(null);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::any())
            ->andReturn($book);

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('sync')->with([]);

        $book->shouldReceive('authors')->andReturn($relation);
        $book->shouldReceive('subjects')->andReturn($relation);

        $createBookDTO = new CreateBookDTO(
            authors: [],
            title: $book->title,
            edition: $book->edition,
            publisher: $book->publisher,
            published_year: $book->published_year,
            subjects: []
        );

        $result = $this->service->create($createBookDTO);

        $this->assertInstanceOf(BookDTO::class, $result);
        $this->assertEquals($book->id, $result->id);
        $this->assertEquals($book->title, $result->title);
        $this->assertEquals($book->edition, $result->edition);
        $this->assertEquals($book->publisher, $result->publisher);
        $this->assertEquals($book->published_year, $result->published_year);
    }

    public function testCreateThrowsConflictIfNameExists()
    {
        $this->expectException(ConflictException::class);

        $book = Mockery::mock(Book::class)->makePartial();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';
        $book->edition = 1;
        $book->publisher = 'Nelvana';
        $book->published_year = 2004;
        $book->created_at = Carbon::now();
        $book->updated_at = Carbon::now();

        $createBookDTO = new CreateBookDTO(
            authors: [],
            title: $book->title,
            edition: $book->edition,
            publisher: $book->publisher,
            published_year: $book->published_year,
            subjects: []
        );

        $this->repository
            ->shouldReceive('findByTitle')
            ->once()
            ->with($book->title)
            ->andReturn($book);

        $this->service->create($createBookDTO);
    }

    public function testUpdateBookSuccessfully()
    {
        $book = Mockery::mock(Book::class)->makePartial();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';
        $book->edition = 1;
        $book->publisher = 'Nelvana';
        $book->published_year = 2004;
        $book->created_at = Carbon::now();
        $book->updated_at = Carbon::now();

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $book->id])
            ->andReturn(collect([$book]));

        $this->repository
            ->shouldReceive('findByTitle')
            ->once()
            ->with($book->title)
            ->andReturn(null);

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('sync')->with([]);

        $book->shouldReceive('authors')->andReturn($relation);
        $book->shouldReceive('subjects')->andReturn($relation);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::any(), $book->id)
            ->andReturn($book);

        $updateBookDTO = new UpdateBookDTO(
            authors: [],
            title: $book->title,
            edition: $book->edition,
            publisher: $book->publisher,
            published_year: $book->published_year,
            subjects: []
        );

        $this->service->update($book->id, $updateBookDTO);

        $this->assertTrue(true);
    }

    public function testUpdateThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $book = new Book();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';
        $book->edition = 1;
        $book->publisher = 'Nelvana';
        $book->published_year = 2004;
        $book->created_at = Carbon::now();
        $book->updated_at = Carbon::now();

        $updateBookDTO = new UpdateBookDTO(
            authors: [],
            title: $book->title,
            edition: $book->edition,
            publisher: $book->publisher,
            published_year: $book->published_year,
            subjects: []
        );

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $book->id])
            ->andReturn(collect());

        $this->service->update($book->id, $updateBookDTO);
    }

    public function testUpdateThrowsConflictWhenNameExistsOnAnotherBook()
    {
        $this->expectException(ConflictException::class);

        $book = Mockery::mock(Book::class)->makePartial();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';
        $book->edition = 1;
        $book->publisher = 'Nelvana';
        $book->published_year = 2004;
        $book->created_at = Carbon::now();
        $book->updated_at = Carbon::now();

        $secondBook = new Book();
        $secondBook->id = 2;
        $secondBook->title = 'Robinson Crusoe';
        $secondBook->edition = 1;
        $secondBook->publisher = 'Teste';
        $secondBook->published_year = 2004;
        $secondBook->created_at = Carbon::now();
        $secondBook->updated_at = Carbon::now();

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $book->id])
            ->andReturn(collect([$book]));

        $this->repository
            ->shouldReceive('findByTitle')
            ->once()
            ->with($secondBook->title)
            ->andReturn($secondBook);

        $updateBookDTO = new UpdateBookDTO(
            authors: [],
            title: $secondBook->title,
            edition: $book->edition,
            publisher: $book->publisher,
            published_year: $book->published_year,
            subjects: []
        );

        $this->service->update($book->id, $updateBookDTO);
    }

    public function testDeleteBookSuccessfully()
    {
        $book = new Book();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $book->id])
            ->andReturn(collect([$book]));

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($book->id);

        $this->service->delete($book->id);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $book = new Book();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $book->id])
            ->andReturn(collect());

        $this->service->delete($book->id);
    }

    public function testFindOneSuccessfully()
    {

        $author = new Author();
        $author->id = 1;
        $author->name = 'Hergé';
        $author->created_at = Carbon::now();
        $author->updated_at = Carbon::now();

        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Aventura';
        $subject->created_at = Carbon::now();
        $subject->updated_at = Carbon::now();

        $book = new Book();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';
        $book->edition = 1;
        $book->publisher = 'Nelvana';
        $book->authors = collect([$author]);
        $book->subjects = collect([$subject]);
        $book->published_year = 2004;
        $book->created_at = Carbon::now();
        $book->updated_at = Carbon::now();

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $book->id])
            ->andReturn(collect([$book]));

        $result = $this->service->findOne($book->id);

        $this->assertInstanceOf(BookDTO::class, $result);
        $this->assertEquals($book->id, $result->id);
        $this->assertEquals($book->title, $result->title);
        $this->assertEquals($book->edition, $result->edition);
        $this->assertEquals($book->publisher, $result->publisher);
        $this->assertEquals($book->published_year, $result->published_year);
    }

    public function testFindOneThrowsNotFound()
    {
        $this->expectException(NotFoundException::class);

        $book = new Book();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(["id" => $book->id])
            ->andReturn(collect());

        $this->service->findOne($book->id);
    }

    public function testFindPaginated()
    {

        $author = new Author();
        $author->id = 1;
        $author->name = 'Hergé';
        $author->created_at = Carbon::now();
        $author->updated_at = Carbon::now();

        $subject = new Subject();
        $subject->id = 1;
        $subject->description = 'Aventura';
        $subject->created_at = Carbon::now();
        $subject->updated_at = Carbon::now();

        $book = new Book();
        $book->id = 1;
        $book->title = 'As Aventuras de Tintim';
        $book->edition = 1;
        $book->publisher = 'Nelvana';
        $book->authors = collect([$author]);
        $book->subjects = collect([$subject]);
        $book->published_year = 2004;
        $book->created_at = Carbon::now();
        $book->updated_at = Carbon::now();

        $page = 1;
        $size = 5;
        $total = 1;
        $paginator = new LengthAwarePaginator([$book], $total, $size, $page);

        $cacheKey = CacheUtil::key("books.paginated", [$page, $size]);

        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, CacheUtil::SHORT, \Closure::class)
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->repository
            ->shouldReceive('findPaginated')
            ->with($page, $size, ["*"], "title")
            ->andReturn($paginator);

        $response = $this->service->findAll($page, $size);

        $this->assertEquals($size, $response->meta->size);
        $this->assertEquals($page, $response->meta->totalPages);
        $this->assertEquals($total, $response->meta->totalResults);

        $this->assertEquals($book->id, $response->data[0]->id);
        $this->assertEquals($book->title, $response->data[0]->title);
        $this->assertEquals($book->edition, $response->data[0]->edition);
        $this->assertEquals($book->publisher, $response->data[0]->publisher);
        $this->assertEquals($book->published_year, $response->data[0]->published_year);


    }

}
