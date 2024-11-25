<?php

namespace Atom\Theme\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MatchCurrentEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== auth()->user()->mail) {
            $fail('The :attribute must match the current email.');
        }
    }
}
