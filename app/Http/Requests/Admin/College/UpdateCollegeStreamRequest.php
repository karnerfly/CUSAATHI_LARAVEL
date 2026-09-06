<?php

namespace App\Http\Requests\Admin\College;

use App\Models\College\CollegeStreamCutoff;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollegeStreamRequest extends FormRequest
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
            'eligibility' => ['required', 'string', 'min:4', 'max:255'],
            'duration' => ['required', 'integer', 'digits:1'],
            'fee_structure' => ['required', 'array'],
            'fee_structure.fee_year' => ['required', 'integer', 'digits:4', 'min:2000'],
            'fee_structure.admission_fee' => ['required', 'numeric', 'min:100', 'max:50000'],
            'fee_structure.total_fee' => ['required', 'numeric', 'min:1000', 'max:2000000'],
            'fee_structure.verified_at' => ['nullable', 'date_format:Y-m-d\TH:i:sP'],
            'cutoffs' => ['required', 'array', 'min:1'],
            // 'cutoffs.*.id' => ['required', 'integer', Rule::exists(CollegeStreamCutoff::class, 'id')],
            'cutoffs.*.category' => ['required', 'string', 'max:10'],
            'cutoffs.*.marks' => ['required', 'numeric', 'min:1', 'max:100'],
            'cutoffs.*.published_at' => ['nullable', 'date_format:Y-m-d\TH:i:sP'],
        ];
    }
}
