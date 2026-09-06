<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $college_stream_id
 * @property int $fee_year
 * @property float $admission_fee
 * @property float $total_fee
 * @property Carbon $verified_at
 */
#[Fillable(['fee_year', 'admission_fee', 'total_fee', 'verified_at'])]
class CollegeStreamFeeStructure extends Model
{
    protected $table = 'college.college_stream_fee_structures';

    public $timestamps = false;

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    function college_stream(): BelongsTo
    {
        return $this->belongsTo(CollegeStream::class);
    }
}
