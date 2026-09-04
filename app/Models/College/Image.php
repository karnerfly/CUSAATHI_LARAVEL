<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $college_id
 * @property string $group
 * @property string $url
 * @property string $alt_text
 * @property Carbon|null $created_at
 */
#[Fillable(['group', 'url', 'alt_text'])]
class Image extends Model
{
    protected $table = 'college.images';

    public const UPDATED_AT = null;

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }
}
