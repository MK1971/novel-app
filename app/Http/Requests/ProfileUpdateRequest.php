<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\UploadFailureMessage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // No Rule::dimensions() — it rejects many valid phone/camera JPEGs; rely on image + max size only.
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Laravel’s default is “The :attribute failed to upload.” — replaced in withValidator when PHP reports an invalid upload.
            'avatar.image' => 'Choose a valid image file.',
            'avatar.mimes' => 'Use JPG, PNG, WebP, or GIF. iPhone HEIC/HEIF is not supported here—export as JPEG in Photos first.',
            'avatar.max' => 'That image is larger than 5 MB. Resize or compress it and try again.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('avatar');
            if (! $file instanceof UploadedFile || $file->isValid()) {
                return;
            }
            $validator->errors()->forget('avatar');
            $validator->errors()->add('avatar', UploadFailureMessage::forInvalidUpload($file));
        });
    }
}
