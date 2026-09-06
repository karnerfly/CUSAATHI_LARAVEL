<?php

namespace App\Http\Resources\Admin\College;

use App\Models\College\CollegeStreamFeeStructure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CollegeStreamFeeStructure
 */
class CollegeStreamFeeStructureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fee_year' => $this->fee_year,
            'admission_fee' => $this->admission_fee,
            'total_fee' => $this->total_fee,
            'verified_at' => $this->verified_at,
        ];
    }
}
