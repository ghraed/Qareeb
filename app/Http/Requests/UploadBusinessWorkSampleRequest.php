<?php

namespace App\Http\Requests;

use App\Models\BusinessProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class UploadBusinessWorkSampleRequest extends FormRequest
{
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
            'photos' => [
                'required',
                'array',
                'min:1',
            ],
            'photos.*' => [
                'required',
                File::image()->max(5120),
                'mimes:jpg,jpeg,png,webp',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $profile = $this->user()?->businessProfile;

            if (! $profile instanceof BusinessProfile) {
                return;
            }

            $existingCount = count($profile->work_samples);
            $uploadedCount = count($this->file('photos', []));
            $remainingSlots = BusinessProfile::MAX_WORK_SAMPLES - $existingCount;

            if ($remainingSlots <= 0) {
                $validator->errors()->add('photos', 'You have reached the 6 image limit. Delete one image to upload another.');

                return;
            }

            if ($uploadedCount > $remainingSlots) {
                $validator->errors()->add('photos', "You can upload up to {$remainingSlots} more image(s) in this request.");
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Please choose at least one work sample image to upload.',
            'photos.array' => 'Please upload work sample images as a single selection.',
            'photos.min' => 'Please choose at least one work sample image to upload.',
            'photos.*.required' => 'Please choose a work sample image to upload.',
            'photos.*.image' => 'Only image files can be uploaded as work samples.',
            'photos.*.max' => 'Each work sample image must be 5 MB or smaller.',
        ];
    }
}
