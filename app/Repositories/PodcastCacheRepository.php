<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PodcastCache;

class PodcastCacheRepository
{
    public function addPodcastCache(string $content): void
    {
        PodcastCache::query()->create([
            'content' => $content,
        ]);
    }

    public function count(): int
    {
        return PodcastCache::query()->count();
    }

    public function getLatest(): PodcastCache|null
    {
        return PodcastCache::query()->latest()->limit(1)->first();
    }

    public function removeStalePodcastCache(PodcastCache|null $latestEntry): void
    {
        if ($latestEntry === null) {
            return;
        }

        PodcastCache::query()->where('id', '!=', $latestEntry->id)->delete();
    }
}
