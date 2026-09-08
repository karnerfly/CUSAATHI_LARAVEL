<?php

namespace App\Http\Requests\Admin\College;

use App\Enums\CollegeType;
use App\Models\College\College;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCollegeRequest extends FormRequest
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
        $cid = $this->route('college')?->id ?? $this->route('college');

        return [
            'name' => ['required', 'string', 'min:4', 'max:200'],
            'description' => ['nullable', 'string', 'min:4', 'max:5000'],
            'slug' => [
                'required',
                'string',
                'min:4',
                'max:200',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(College::class, 'slug')->ignore($cid),
            ],
            'type' => ['required', 'string', Rule::enum(CollegeType::class)],
            // 'thumbnail_url' => ['nullable', 'string', 'url'],
            'website_url' => ['required', 'string', 'url'],
            'established_year' => ['required', 'integer', 'digits:4', 'min:1800'],
            'accreditation_body' => ['required', 'string', 'uppercase'],
            'accreditation_grade' => ['required', 'string', 'regex:/^[A-F](\+{0,2})$/'],
            'accreditation_year' => ['required', 'integer', 'digits:4', 'min:1800'],
            'accreditation_value' => ['required', 'numeric', 'min:0', 'max:10'],
            'contact_details' => ['nullable', 'json'],
        ];
    }
}
