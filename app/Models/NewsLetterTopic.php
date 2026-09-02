<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug'])]
class NewsLetterTopic extends Model
{
    protected $table = 'newsletter_topics';

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            NewsletterSubscriber::class,
            'newsletter_subscriber_topic',
            'topic_id',
            'subscriber_id',
        );
    }

    public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class, 'topic_id');
    }
}
