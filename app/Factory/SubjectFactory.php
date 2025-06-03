<?php

namespace App\Factory;

use App\Http\DTO\Subject\SubjectDTO;
use App\Http\DTO\Subject\CreateSubjectDTO;
use App\Http\DTO\Subject\UpdateSubjectDTO;
use App\Http\Requests\API\Subject\StoreSubjectFormRequest;
use App\Http\Requests\API\Subject\UpdateSubjectFormRequest;
use App\Models\Subject;

class SubjectFactory
{
    public static function fromStoreRequest(StoreSubjectFormRequest $request): CreateSubjectDTO
    {
        return new CreateSubjectDTO(
            description: $request->input("description")
        );
    }

    public static function fromUpdateRequest(UpdateSubjectFormRequest $request): UpdateSubjectDTO
    {
        return new UpdateSubjectDTO(
            description: $request->input("description")
        );
    }

    public static function fromModel(Subject $subject): SubjectDTO
    {
        return new SubjectDTO(
            id: $subject->id,
            description: $subject->description,
            created_at: $subject->created_at->toIso8601String(),
            updated_at: $subject->updated_at->toIso8601String()
        );
    }
}
