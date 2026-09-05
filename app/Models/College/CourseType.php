<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $label
 * @property string $slug
 */
#[Fillable(['label', 'slug'])]
class CourseType extends Model
{
    protected $table = 'college.course_types';

    public $timestamps = false;

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
