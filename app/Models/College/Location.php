<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property int $college_id
 * @property string $address_line_1
 * @property string|null $address_line_2
 * @property string $pincode
 * @property string $district
 * @property string $area_zone
 * @property string $locality_tag
 * @property string $google_map_url
 * @property Carbon|null $created_at
 */
#[Fillable(['address_line_1', 'address_line_2', 'pincode', 'district', 'area_zone', 'locality_tag', 'google_map_url'])]
class Location extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'college.locations';

    public const UPDATED_AT = null;

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }
}
