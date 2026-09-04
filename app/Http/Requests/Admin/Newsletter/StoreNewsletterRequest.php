<?php

namespace App\Http\Requests\Admin\Newsletter;

use App\Models\Newsletter\Topic;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsletterRequest extends FormRequest
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
            'subject' => ['required', 'string', 'min:4', 'max:255'],
            'topic_id' => ['nullable', 'integer', Rule::exists(Topic::class, 'id')],
            'content' => ['required', 'string', 'min:4'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * Custom attribute names for validation keys.
     */
    public function attributes(): array
    {
        return [
            'topic_id' => 'topic',
        ];
    }
}
