<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property string $slug
 */
#[Fillable(['course_id', 'name', 'slug'])]
class Stream extends Model
{
    protected $table = 'college.streams';

    public $timestamps = false;

    public function colleges(): BelongsToMany
    {
        return $this->belongsToMany(College::class)
            ->using(CollegeStream::class)
            ->withPivot(['id', 'eligibility', 'duration']);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
