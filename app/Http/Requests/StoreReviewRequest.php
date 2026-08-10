<?php

namespace App\Http\Requests;

use App\Models\BusinessProfile;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var BusinessProfile $profile */
            $profile = $this->route('provider')->businessProfile;

            if ($this->user()?->id === $profile->user_id) {
                $validator->errors()->add('rating', 'You cannot review your own business.');
            }

            if ($profile->reviews()->where('reviewer_id', $this->user()?->id)->exists()) {
                $validator->errors()->add('rating', 'You have already reviewed this business.');
            }
        });
    }
}
