<?php

namespace App\Http\Requests;

use App\Models\BusinessProfile;
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => BusinessProfile::editableRules([])['phone'],
            'district' => ['nullable', 'string', Rule::in(BusinessProfile::LEBANON_DISTRICTS)],
            'city' => ['nullable', 'string', Rule::in(BusinessProfile::citiesForDistricts(is_string($this->input('district')) ? [$this->input('district')] : []))],
            ...AccountIdentity::registrationRules($this->user()->id),
            'current_password' => [Rule::requiredIf($identityChanged), 'nullable', 'current_password:web'],
        ];
    }
}
