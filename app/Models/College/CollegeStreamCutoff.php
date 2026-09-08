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
 * @property int $college_stream_id
 * @property string $category
 * @property float $marks
 * @property Carbon $published_at
 */
#[Fillable(['category', 'marks', 'published_at'])]
class CollegeStreamCutoff extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'college.college_stream_cutoffs';

    public $timestamps = false;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function college_stream(): BelongsTo
    {
        return $this->belongsTo(CollegeStream::class);
    }
}
