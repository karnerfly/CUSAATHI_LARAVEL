<?php

namespace App\Models\College;

use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string $type
 * @property string|null $thumbnail_url
 * @property string $website_url
 * @property int $established_year
 * @property string $accreditation_body
 * @property string $accreditation_grade
 * @property int $accreditation_year
 * @property float $accreditation_value
 * @property string|null $contact_details
 * @property Carbon|null $verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[
    Fillable([
        'name',
        'description',
        'slug',
        'type',
        'thumbnail_url',
        'website_url',
        'established_year',
        'accreditation_body',
        'accreditation_grade',
        'accreditation_year',
        'accreditation_value',
        'contact_details',
        'verified_at',
    ]),
]
class College extends Model implements Auditable
{
    use AuditableTrait, SoftDeletes;

    protected $table = 'college.colleges';

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function streams(): BelongsToMany
    {
        return $this->belongsToMany(Stream::class)
            ->using(CollegeStream::class)
            ->withPivot(['id', 'eligibility', 'duration']);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }
}
