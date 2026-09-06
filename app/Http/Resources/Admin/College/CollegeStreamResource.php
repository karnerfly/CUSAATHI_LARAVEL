<?php

namespace App\Http\Resources\Admin\College;

use App\Models\College\CollegeStream;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CollegeStream
 */
class CollegeStreamResource extends JsonResource
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
            'eligibility' => $this->pivot->eligibility,
            'duration' => $this->pivot->duration,
            'fee_structure' => CollegeStreamFeeStructureResource::make($this->pivot->fee_structure),
            'cutoffs' => CollegeStreamCutoffsResource::collection($this->pivot->cutoffs),
        ];
    }
}
