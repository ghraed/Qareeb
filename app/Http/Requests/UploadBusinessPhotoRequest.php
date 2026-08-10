<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadBusinessPhotoRequest extends FormRequest
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
            'photo' => [
                'required',
                File::types(['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif', 'avif'])->max(10240),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Please choose an image to upload.',
            'photo.max' => 'The image must be 10 MB or smaller.',
            'photo.types' => 'Please upload a JPG, PNG, WEBP, HEIC, HEIF, or AVIF image.',
        ];
    }
}
