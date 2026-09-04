<?php

namespace App\Http\Resources\Admin\College;

use App\Models\College\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Location
 */
class CollegeLocationResource extends JsonResource
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
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'pincode' => $this->picode,
            'district' => $this->district,
            'area_zone' => $this->area_zone,
            'loclity_tag' => $this->locality_tag,
            'google_map_url' => $this->google_map_url,
            'created_at' => $this->created_at,
        ];
    }
}
