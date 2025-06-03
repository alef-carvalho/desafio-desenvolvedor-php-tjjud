<?php

namespace App\Http\Requests\API\Subject;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectFormRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "description" => ["required", "string", "min:3", "max:20"],
        ];
    }

    public function attributes(): array
    {
        return [
            "description" => "assunto",
        ];
    }
}
