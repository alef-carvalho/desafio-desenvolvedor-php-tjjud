<?php

namespace App\Factory;

use App\Http\DTO\Author\AuthorDTO;
use App\Http\DTO\Author\CreateAuthorDTO;
use App\Http\DTO\Author\UpdateAuthorDTO;
use App\Http\Requests\API\Author\StoreAuthorFormRequest;
use App\Http\Requests\API\Author\UpdateAuthorFormRequest;
use App\Models\Author;

class AuthorFactory
{
    public static function fromStoreRequest(StoreAuthorFormRequest $request): CreateAuthorDTO
    {
        return new CreateAuthorDTO(
            name: $request->input("name")
        );
    }

    public static function fromUpdateRequest(UpdateAuthorFormRequest $request): UpdateAuthorDTO
    {
        return new UpdateAuthorDTO(
            name: $request->input("name")
        );
    }

    public static function fromModel(Author $author): AuthorDTO
    {
        return new AuthorDTO(
            id: $author->id,
            name: $author->name,
            created_at: $author->created_at->toIso8601String(),
            updated_at: $author->updated_at->toIso8601String()
        );
    }
}
