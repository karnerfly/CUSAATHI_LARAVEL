<?php

namespace App\Models\College;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $college_id
 * @property string $title
 * @property string|null $description
 * @property string $curriculum
 * @property string $category
 * @property int $semester
 * @property string $resource_url
 * @property Carbon $published_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[
    Fillable([
        'college_id',
        'title',
        'description',
        'curriculum',
        'category',
        'semester',
        'resource_url',
        'published_date',
    ]),
]
class Notice extends Model
{
    use SoftDeletes;

    protected $table = 'college.notices';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_date' => 'datetime:Y-m-d',
        ];
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }
}
