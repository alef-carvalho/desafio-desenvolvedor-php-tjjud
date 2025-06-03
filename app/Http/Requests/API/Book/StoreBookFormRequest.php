<?php

namespace App\Http\Requests\API\Book;

use App\Rules\IsValidBookName;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookFormRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title"          => ["required", "string", "min:3", "max:40", new IsValidBookName()],
            "edition"        => ["required", "integer", "min:1"],
            "authors"        => ["required", "array", "min:1"],
            "authors.*"      => ["integer"],
            "subjects"       => ["required", "array", "min:1"],
            "subjects.*"     => ["integer"],
            "publisher"      => ["required", "string", "min:3", "max:40"],
            "published_year" => ["required", "string", "date_format:Y"],
        ];
    }

    public function attributes(): array
    {
        return [
            "title"          => "título",
            "edition"        => "edição",
            "authors"        => "autores",
            "subjects"       => "assuntos",
            "publisher"      => "editora",
            "published_year" => "ano de publicação",
        ];
    }
}
