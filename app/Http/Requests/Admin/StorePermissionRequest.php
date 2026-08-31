<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name' => ['required', 'string', 'lowercase', 'min:5', 'max:100'],
            'ability' => ['required', 'string', 'lowercase', 'min:5', 'max:100', 'regex:/^[a-z]+:[a-z]+$/'],
            'category' => ['required', 'string', 'lowercase', 'min:5', 'max:50', 'regex:/^[a-z]+$/'],
        ];
    }
}
