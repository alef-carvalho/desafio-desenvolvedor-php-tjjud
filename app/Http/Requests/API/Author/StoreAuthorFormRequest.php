<?php

namespace App\Http\Requests\API\Author;

use App\Rules\IsValidPersonName;
use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => ["required", "string", "min:3", "max:40", new IsValidPersonName()]
        ];
    }

    public function attributes(): array
    {
        return [
            "name" => "nome"
        ];
    }
}
