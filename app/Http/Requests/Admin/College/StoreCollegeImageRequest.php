<?php

namespace App\Http\Requests\Admin\College;

use App\Enums\CollegeImageGroup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCollegeImageRequest extends FormRequest
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
            'url' => ['required', 'string', 'url'],
            // 'college_id' => ['required', 'integer', Rule::exists(College::class, 'id')],
            'group' => ['required', 'string', Rule::enum(CollegeImageGroup::class)],
            'alt_text' => ['required', 'string', 'min:4', 'max:100'],
        ];
    }

    /**
     * Custom attribute names for validation keys.
     */
    // public function attributes(): array
    // {
    //     return [
    //         'college_id' => 'college',
    //     ];
    // }
}
