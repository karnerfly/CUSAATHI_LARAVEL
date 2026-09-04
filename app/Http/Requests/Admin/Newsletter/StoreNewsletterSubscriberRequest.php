<?php

namespace App\Http\Requests\Admin\Newsletter;

use App\Models\Newsletter\Subscriber;
use App\Models\Newsletter\Topic;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $sid = $this->route('subscriber')?->id ?? $this->route('subscriber');

        return [
            'email' => ['required', 'email', 'max:255', Rule::unique(Subscriber::class)->ignore($sid)],
            'active' => ['required', 'boolean'],
            'verified' => ['nullable', 'boolean'],
            'topic_ids' => ['nullable', 'array', 'min:1'],
            'topic_ids.*' => ['integer', 'distinct', Rule::exists(Topic::class, 'id')],
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
