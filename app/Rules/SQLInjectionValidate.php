<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SQLInjectionValidate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value && preg_match('/\b(SELECT|INSERT|DELETE|UPDATE|DROP|UNION|WHERE|FROM|--|\/\*|\*\/)\b/i', $value)) {
            $fail('Không được chứa câu lệnh SQL.');
        }
    }
}
