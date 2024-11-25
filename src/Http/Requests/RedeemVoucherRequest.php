<?php

namespace Atom\Theme\Http\Requests;

use Atom\Theme\Rules\VoucherValid;
use Illuminate\Foundation\Http\FormRequest;

class RedeemVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'exists:website_shop_vouchers,code', new VoucherValid],
            // 'category_id' => ['required', 'exists:website_help_center_categories,id'],
            // 'title' => ['required', 'string', 'min:10', 'max:255'],
            // 'content' => ['required', 'string', 'min:10', 'max:65000'],
        ];
    }
}
