<?php

namespace App\Http\Requests;

use App\Models\BusinessProfile;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProviderDirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['category' => BusinessProfile::normalizeCategoryFilter($this->input('category'))]);
    }

    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', Rule::in(BusinessProfile::WORK_CATEGORIES)],
            'district' => ['bail', 'nullable', 'required_with:city', 'string', Rule::in(BusinessProfile::LEBANON_DISTRICTS)],
            'city' => ['bail', 'nullable', 'string', Rule::in(BusinessProfile::citiesForDistricts(is_string($this->input('district')) ? [$this->input('district')] : []))],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(redirect()->route('providers.index')->withErrors($validator)->withInput($this->only(['category', 'district', 'city'])));
    }
}
