<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|PodcastCache newModelQuery()
 * @method static Builder<static>|PodcastCache newQuery()
 * @method static Builder<static>|PodcastCache query()
 * @method static Builder<static>|PodcastCache whereContent($value)
 * @method static Builder<static>|PodcastCache whereCreatedAt($value)
 * @method static Builder<static>|PodcastCache whereId($value)
 * @method static Builder<static>|PodcastCache whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PodcastCache extends Model
{
    protected $table = 'podcast_cache';

    /**
     * @var string[]
     */
    protected $fillable = [
        'content',
    ];

    public $timestamps = true;

    protected $casts = [
        'content' => 'string',
    ];
}
