<?php

namespace App\Http\Requests\Admin\Notice;

use App\Enums\NoticeCategory;
use App\Enums\NoticeCurriculum;
use App\Models\College\College;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNoticeRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:4', 'max:200'],
            'description' => ['nullable', 'string', 'min:4', 'max:5000'],
            'college_id' => ['nullable', 'integer', Rule::exists(College::class, 'id')],
            'curriculum' => ['required', 'string', Rule::enum(NoticeCurriculum::class)],
            'category' => ['required', 'string', Rule::enum(NoticeCategory::class)],
            'semester' => ['required', 'integer', 'between:1,8'],
            'resource_url' => ['required', 'string', 'url'],
            'published_date' => ['required', 'string', 'date'],
        ];
    }

    /**
     * Custom attribute names for validation keys.
     */
    public function attributes(): array
    {
        return [
            'college_id' => 'college',
        ];
    }
}
