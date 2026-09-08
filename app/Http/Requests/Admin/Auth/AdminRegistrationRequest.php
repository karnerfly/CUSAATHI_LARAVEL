<?php

namespace App\Http\Requests\Admin\Auth;

use App\Models\AdminRegistration;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRegistrationRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:4', 'max:100', 'regex:/^[A-Za-z]+(?: [A-Za-z]+)*$/'],
            'email' => ['required', 'email', 'max:255', Rule::unique(AdminRegistration::class)],
        ];
    }
}
