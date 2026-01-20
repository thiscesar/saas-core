<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Invitation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueInvitation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $invitation = Invitation::where('email', $value)->first();

        if (! $invitation) {
            return;
        }

        if ($invitation->isValid()) {
            $fail('Já existe um convite pendente para este email.');
        }
    }
}
