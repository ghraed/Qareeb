<?php

namespace App\Http\Requests;

use App\Support\AccountIdentity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class BusinessRegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->input('email')) ? AccountIdentity::normalize($this->input('email')) : $this->input('email'),
            'username' => is_string($this->input('username')) ? AccountIdentity::normalize($this->input('username')) : $this->input('username'),
        ]);
    }

    public function termsConsent(): array
    {
        return ['terms_accepted_at' => now(), 'terms_version' => config('terms.version')];
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            ...AccountIdentity::registrationRules(),
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms_accepted' => ['accepted'],
            'terms_version' => ['required', Rule::in([config('terms.version')])],
        ];
    }
}
