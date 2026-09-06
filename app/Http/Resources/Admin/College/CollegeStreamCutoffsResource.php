<?php

namespace App\Http\Resources\Admin\College;

use App\Models\College\CollegeStreamCutoff;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CollegeStreamCutoff
 */
class CollegeStreamCutoffsResource extends JsonResource
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
            'category' => $this->category,
            'marks' => $this->marks,
            'published_at' => $this->published_at,
        ];
    }
}
