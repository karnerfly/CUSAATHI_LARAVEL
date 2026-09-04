<?php

namespace App\Http\Requests\Admin\College;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCollegeLocationRequest extends FormRequest
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
            'address_line_1' => ['required', 'string', 'min:4', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'min:4', 'max:255'],
            // 'college_id' => ['required', 'integer', Rule::exists(College::class, 'id')],
            'pincode' => ['required', 'string', 'size:6'],
            'district' => ['required', 'string', 'min:4', 'max:100'],
            'area_zone' => ['required', 'string', 'min:4', 'max:20'],
            'locality_tag' => ['required', 'string', 'min:4', 'max:20'],
            'google_map_url' => ['required', 'string', 'url'],
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
