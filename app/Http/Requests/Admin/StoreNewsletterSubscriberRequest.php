<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewsletterSubscriberRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255', "unique:newsletter_subscribers,email,{$subscriber_id}"],
            'active' => ['required', 'boolean'],
            'verified' => ['nullable', 'boolean'],
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
