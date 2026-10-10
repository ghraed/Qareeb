<?php

namespace App\Http\Requests;

use App\Models\BusinessProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasActiveProviderAccount() && $this->user()->businessProfile !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/\A\+?[0-9][0-9 ()-]{5,48}\z/'],
            'description' => ['nullable', 'string', 'max:3000'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', Rule::in(BusinessProfile::WORK_CATEGORIES)],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'array'],
            'city.*' => ['string', Rule::in([
                '__all__',
                ...BusinessProfile::citiesForDistricts($this->array('state')),
            ])],
            'state' => ['nullable', 'array'],
            'state.*' => ['string', Rule::in(BusinessProfile::LEBANON_DISTRICTS)],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'is_available_for_hire' => ['nullable', 'boolean'],
        ];
    }
}
