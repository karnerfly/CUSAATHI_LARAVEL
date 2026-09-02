<?php

namespace App\Http\Requests\Admin;

use App\Enums\NewsLetterFrequency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsletterSubscriberRequest extends FormRequest
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
        $subscriber_id = $this->route('subscriber')?->id ?? $this->route('subscriber');

        return [
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('newsletter_subscribers', 'email')->ignore($subscriber_id),
            ],
            'frequency' => ['sometimes', 'required', 'string', Rule::enum(NewsLetterFrequency::class)],
            'active' => ['sometimes', 'required', 'boolean'],
            'verified' => ['sometimes', 'required', 'boolean'],
            'topic_ids' => ['sometimes', 'array'],
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
