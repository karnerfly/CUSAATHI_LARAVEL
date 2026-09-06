<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property int $college_id
 * @property int $stream_id
 * @property string $eligibility
 * @property int $duration
 */
#[Fillable(['college_id', 'stream_id', 'eligibility', 'duration'])]
class CollegeStream extends Pivot implements Auditable
{
    use AuditableTrait;

    protected $table = 'college.college_stream';

    public $timestamps = false;

    public function fee_structure(): HasOne
    {
        return $this->hasOne(CollegeStreamFeeStructure::class, 'college_stream_id');
    }

    public function cutoffs(): HasMany
    {
        return $this->hasMany(CollegeStreamCutoff::class, 'college_stream_id');
    }
}
