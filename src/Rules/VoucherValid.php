<?php

namespace Atom\Theme\Rules;

use Atom\Core\Models\WebsiteShopVoucher;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VoucherValid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $voucher = WebsiteShopVoucher::firstWhere('code', $value);

        if (is_null($voucher)) {
            $fail('The :attribute is invalid.');

            return;
        }

        if ($voucher->expires_at?->isPast()) {
            $fail('The :attribute has expired.');

            return;
        }

        if ($voucher->max_uses > 0 && $voucher->redeems->count() >= $voucher->max_uses) {
            $fail('The :attribute has reached its maximum uses.');

            return;
        }
    }
}
