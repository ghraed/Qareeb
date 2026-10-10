<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\AccountIdentity;
use Illuminate\Validation\Rule;

class SeekerProfileRequest extends BusinessRegisterRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSeeker() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->input('email', $this->user()?->email),
            'username' => $this->input('username', $this->user()?->username),
        ]);
        parent::prepareForValidation();
    }

    public function rules(): array
    {
        $identityChanged = $this->input('email') !== $this->user()->email || $this->input('username') !== $this->user()->username;

        return [
            ...User::seekerProfileRules($this->input('district')),
            ...AccountIdentity::registrationRules($this->user()->id),
            'current_password' => [Rule::requiredIf($identityChanged), 'nullable', 'current_password:web'],
        ];
    }
}
