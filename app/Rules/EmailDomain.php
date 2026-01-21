<?php

declare(strict_types = 1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedDomain = config('app.user_email_domain');

        if (empty($allowedDomain)) {
            return;
        }

        $emailDomain = substr(strrchr((string) $value, '@'), 1);

        if ($emailDomain !== $allowedDomain) {
            $fail("O email deve ser do domínio {$allowedDomain}.");
        }
    }
}
