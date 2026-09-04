<?php

namespace App\Http\Requests\Admin\College;

use App\Enums\CollegeType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCollegeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('deleted')) {
            $this->merge([
                'deleted' => filter_var($this->deleted, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:200'],
            'type' => ['sometimes', 'string', Rule::enum(CollegeType::class)],
            'established_year' => ['sometimes', 'integer', 'digits:4', 'min:1800'],
            'accreditation_grade' => ['sometimes', 'string', 'regex:/^[A-F](\+{0,2})$/'],
            'deleted' => ['required', 'boolean'],
            'order' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
