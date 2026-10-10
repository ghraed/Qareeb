<?php

namespace App\Http\Requests;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSeeker() ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(Complaint::TYPES)],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'submission_token' => ['required', 'uuid'],
            'provider_id' => ['bail', 'nullable', 'integer', function (string $attribute, mixed $value, \Closure $fail): void {
                $provider = User::find($value);
                if (! $provider?->hasActiveProviderAccount() || ! $provider->businessProfile) {
                    $fail('Please choose a currently listed provider.');
                }
            }],
        ];
    }
}
