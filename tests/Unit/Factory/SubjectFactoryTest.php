<?php

namespace Factory;

use App\Factory\AuthorFactory;
use App\Factory\SubjectFactory;
use App\Http\DTO\Author\AuthorDTO;
use App\Http\DTO\Subject\CreateSubjectDTO;
use App\Http\DTO\Subject\UpdateSubjectDTO;
use App\Http\Requests\API\Subject\StoreSubjectFormRequest;
use App\Http\Requests\API\Subject\UpdateSubjectFormRequest;
use App\Models\Author;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class SubjectFactoryTest extends TestCase
{
    use WithFaker;

    public function testFromStoreRequest()
    {
        $request = Mockery::mock(StoreSubjectFormRequest::class);
        $request->shouldReceive('input')
            ->with('description')
            ->andReturn('Ficção');

        $dto = SubjectFactory::fromStoreRequest($request);

        $this->assertInstanceOf(CreateSubjectDTO::class, $dto);
        $this->assertEquals('Ficção', $dto->description);
    }

    public function testFromUpdateRequest()
    {
        $request = Mockery::mock(UpdateSubjectFormRequest::class);
        $request->shouldReceive('input')
            ->with('description')
            ->andReturn('Ficção');

        $dto = SubjectFactory::fromUpdateRequest($request);

        $this->assertInstanceOf(UpdateSubjectDTO::class, $dto);
        $this->assertEquals('Ficção', $dto->description);
    }

    public function testFromModel()
    {
        $author = new Author();
        $author->id = 1;
        $author->name = 'Isaac Asimov';
        $author->created_at = Carbon::now();
        $author->updated_at = Carbon::now();

        $dto = AuthorFactory::fromModel($author);

        $this->assertInstanceOf(AuthorDTO::class, $dto);
        $this->assertEquals($author->id, $dto->id);
        $this->assertEquals($author->name, $dto->name);
    }

}
