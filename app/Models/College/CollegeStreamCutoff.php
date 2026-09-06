<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $college_stream_id
 * @property string $category
 * @property float $marks
 * @property Carbon $published_at
 */
#[Fillable(['category', 'marks', 'published_at'])]
class CollegeStreamCutoff extends Model
{
    protected $table = 'college.college_stream_cutoffs';

    public $timestamps = false;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    function college_stream(): BelongsTo
    {
        return $this->belongsTo(CollegeStream::class);
    }
}
