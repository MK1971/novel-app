<?php

namespace App\Http\Requests;

use App\Support\ReservedPublicProfileSlugs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicProfileSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'public_profile_enabled' => ['boolean'],
            'leaderboard_visible' => ['boolean'],
            'profile_indexable' => ['boolean'],
            'public_slug' => [
                'nullable',
                'string',
                'max:48',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('users', 'public_slug')->ignore($this->user()->id),
                Rule::requiredIf(fn () => $this->boolean('public_profile_enabled')),
                Rule::notIn(ReservedPublicProfileSlugs::all()),
            ],
            'profile_bio' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->input('public_slug');
        if (is_string($slug)) {
            $slug = trim($slug);
            $slug = $slug !== '' ? Str::slug($slug) : null;
        } else {
            $slug = null;
        }

        $this->merge([
            'public_slug' => $slug,
            'public_profile_enabled' => $this->boolean('public_profile_enabled'),
            'leaderboard_visible' => $this->boolean('leaderboard_visible'),
            'profile_indexable' => $this->boolean('profile_indexable'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'public_slug.not_in' => __('That profile URL is reserved. Pick a different one.'),
        ];
    }
}
