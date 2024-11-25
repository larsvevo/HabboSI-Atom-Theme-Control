<?php

namespace Atom\Theme\Http\Requests;

use Atom\Theme\Rules\MatchCurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class PasswordStoreRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_current' => ['required', new MatchCurrentPassword],
        ];
    }
}
