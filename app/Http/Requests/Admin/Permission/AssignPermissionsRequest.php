<?php

namespace App\Http\Requests\Admin\Permission;

use App\Models\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignPermissionsRequest extends FormRequest
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
            'permission_ids' => ['required', 'array', 'min:1', 'max:25'],
            'permission_ids.*' => ['integer', 'distinct', Rule::exists(Permission::class, 'id')],
        ];
    }

    /**
     * Custom attribute names for validation keys.
     */
    public function attributes(): array
    {
        return [
            'permission_ids' => 'permissions',
            'permission_ids.*' => 'permission',
        ];
    }

    /**
     * Custom message definitions (optional).
     */
    public function messages(): array
    {
        return [
            'permission_ids.*.exists' => 'The :attribute is invalid.',
            'permission_ids.*.distinct' => 'The :attribute list contains duplicate entries.',
        ];
    }
}
