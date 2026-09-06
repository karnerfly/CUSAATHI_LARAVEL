<?php

namespace App\Http\Requests\Admin\College;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCollegeStreamCutoffRequest extends FormRequest
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
            '*.category' => ['required', 'string', 'max:10'],
            '*.marks' => ['required', 'numeric', 'min:1', 'max:100'],
            '*.published_at' => ['nullable', 'date_format:Y-m-d\TH:i:sP'],
        ];
    }
}
