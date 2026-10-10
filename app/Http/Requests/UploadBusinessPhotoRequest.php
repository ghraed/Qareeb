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
                File::image()->max(10240),
                'mimes:jpg,jpeg,png,webp',
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
            'photo.mimes' => 'Please upload a JPG, PNG, or WEBP image.',
        ];
    }
}
