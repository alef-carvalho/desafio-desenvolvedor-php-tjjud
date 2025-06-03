<?php

namespace Factory;

use App\Factory\AuthorFactory;
use App\Http\DTO\Author\AuthorDTO;
use App\Http\DTO\Author\CreateAuthorDTO;
use App\Http\DTO\Author\UpdateAuthorDTO;
use App\Http\Requests\API\Author\StoreAuthorFormRequest;
use App\Http\Requests\API\Author\UpdateAuthorFormRequest;
use App\Models\Author;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class AuthorFactoryTest extends TestCase
{
    use WithFaker;

    public function testFromStoreRequest()
    {
        $request = Mockery::mock(StoreAuthorFormRequest::class);
        $request->shouldReceive('input')
            ->with('name')
            ->andReturn('Ernest Hemingway');

        $dto = AuthorFactory::fromStoreRequest($request);

        $this->assertInstanceOf(CreateAuthorDTO::class, $dto);
        $this->assertEquals('Ernest Hemingway', $dto->name);
    }

    public function testFromUpdateRequest()
    {
        $request = Mockery::mock(UpdateAuthorFormRequest::class);
        $request->shouldReceive('input')
            ->with('name')
            ->andReturn('George Orwell');

        $dto = AuthorFactory::fromUpdateRequest($request);

        $this->assertInstanceOf(UpdateAuthorDTO::class, $dto);
        $this->assertEquals('George Orwell', $dto->name);
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
