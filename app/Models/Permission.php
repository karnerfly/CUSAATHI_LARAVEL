<?php

namespace App\Models;

use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

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
