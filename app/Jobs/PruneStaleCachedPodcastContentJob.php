<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use App\Repositories\PodcastCacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PruneStaleCachedPodcastContentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Tenant $tenant)
    {
    }

    public function handle(PodcastCacheRepository $podcastCacheRepository): void
    {
        tenancy()->initialize($this->tenant);
        Log::withContext(['tenant' => $this->tenant->id]);
        Log::info(sprintf('Pruning stale cached podcast content for: %s', $this->tenant->id));

        if ($podcastCacheRepository->count() === 1) {
            Log::info(sprintf('Only one entry found for tenant %s Skipping', $this->tenant->id));
            return;
        }

        $latestEntry = $podcastCacheRepository->getLatest();
        if ($latestEntry === null) {
            Log::info(sprintf('No cached podcast content found for tenant: %s', $this->tenant->id));
            return;
        }

        Log::info(sprintf('Deleting stale cached podcast content for: %s', $this->tenant->id));
        $podcastCacheRepository->removeStalePodcastCache($latestEntry);

        Log::info('Stale podcast content pruned successfully.');
        tenancy()->end();
    }
}
