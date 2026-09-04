<?php

namespace App\Http\Requests\Admin\Notice;

use App\Enums\NoticeCategory;
use App\Enums\NoticeCurriculum;
use App\Models\Notice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @mixin Notice
 */
class IndexNoticeRequest extends FormRequest
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
            'curriculum' => ['sometimes', 'string', Rule::enum(NoticeCurriculum::class)],
            'category' => ['sometimes', 'string', Rule::enum(NoticeCategory::class)],
            'semester' => ['sometimes', 'integer', 'between:1,8'],
            'deleted' => ['required', 'boolean'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date', 'after_or_equal:from'],
            'order' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
