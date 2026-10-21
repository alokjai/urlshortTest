<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && !$user->hasRole('SuperAdmin');
    }

    public function rules(): array
    {
        return [
            'original_url' => 'required|url|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'Please provide a valid URL.',
            'original_url.url' => 'The URL format is invalid.',
            'original_url.max' => 'The URL may not be greater than 2048 characters.',
        ];
    }
}
