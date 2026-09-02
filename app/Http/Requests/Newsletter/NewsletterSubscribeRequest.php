<?php

namespace App\Http\Requests\Newsletter;

use App\Enums\NewsLetterFrequency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsletterSubscribeRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255'],
            'frequency' => ['required', 'string', Rule::enum(NewsLetterFrequency::class)],
            'topic_ids' => ['nullable', 'array', 'min:1'],
            'topic_ids.*' => ['integer', 'distinct', 'exists:newsletter_topics,id'],
        ];
    }

    /**
     * Custom attribute names for validation keys.
     */
    public function attributes(): array
    {
        return [
            'topic_ids' => 'topics',
            'topic_ids.*' => 'topic',
        ];
    }

    /**
     * Custom message definitions (optional).
     */
    public function messages(): array
    {
        return [
            'topic_ids.*.exists' => 'The :attribute is invalid.',
            'topic_ids.*.distinct' => 'The :attribute list contains duplicate entries.',
        ];
    }
}
