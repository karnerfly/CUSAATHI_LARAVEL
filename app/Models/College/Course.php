<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property int $course_type_id
 * @property string $name
 * @property string $code
 */
#[Fillable(['course_type_id', 'name', 'code'])]
class Course extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'college.courses';

    public $timestamps = false;

    public function course_type(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class);
    }
}
