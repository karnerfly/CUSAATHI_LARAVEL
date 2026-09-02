<?php

namespace App\Models;

use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $name
 * @property string $ability
 * @property string $category
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'ability', 'category'])]
class Permission extends Model implements Auditable
{
    /** @use HasFactory<PermissionFactory> */
    use AuditableTrait, HasFactory;

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class)->withPivot(['active', 'created_at']);
    }
}
