<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $subject
 * @property int|null $topic_id
 * @property string $subject
 * @property Carbon|null $scheduled_for
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['subject', 'topic_id', 'content', 'scheduled_for', 'sent_at'])]
class NewsLetter extends Model
{
    protected $table = 'newsletters';

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(NewsletterTopic::class, 'topic_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterLog::class, 'newsletter_id');
    }
}
