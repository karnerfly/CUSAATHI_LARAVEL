<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexAuditRequest extends FormRequest
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
            'query' => ['sometimes', 'string', 'max:100'],
            'event' => ['sometimes', 'string', 'exists:audits,event'],
            'actor_id' => ['sometimes', 'integer', 'exists:admins,id'],
            'actor_type' => ['sometimes', 'string', 'max:255'],
            'auditable_type' => ['sometimes', 'string', 'max:255'],
            'auditable_id' => ['sometimes', 'integer'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date', 'after_or_equal:from'],
            'sort' => ['sometimes', 'in:created_at,event,user_id,auditable_id'],
            'order' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
