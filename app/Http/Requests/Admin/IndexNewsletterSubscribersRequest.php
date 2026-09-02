<?php

namespace App\Http\Requests\Admin;

use App\Enums\NewsLetterFrequency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexNewsletterSubscribersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'string', 'max:255'],
            'user_id' => ['sometimes', 'integer'],
            'active' => ['sometimes', 'boolean'],
            'frequency' => ['sometimes', 'string', Rule::enum(NewsLetterFrequency::class)],
            'verified' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
