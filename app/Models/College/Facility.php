<?php

namespace App\Models\College;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $type
 * @property string $label
 * @property string $note
 */
#[Fillable(['type', 'label', 'note'])]
class Facility extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'college.facilities';

    public $timestamps = false;

    public function colleges(): BelongsToMany
    {
        return $this->belongsToMany(College::class);
    }
}
