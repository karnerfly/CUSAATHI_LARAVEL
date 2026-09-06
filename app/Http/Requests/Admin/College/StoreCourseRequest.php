<?php

namespace App\Http\Requests\Admin\College;

use App\Models\College\CourseType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
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
            'course_type_id' => ['required', 'integer', Rule::exists(CourseType::class, 'id')],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20'],
        ];
    }
}
