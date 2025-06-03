<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class
IsValidBookName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^[A-Za-zÀ-ÿ\s]+$/u', $value)) {
            $fail('custom.book.name')->translate();
        }
    }
}
