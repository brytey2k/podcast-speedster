<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PodcastCache;
use Illuminate\Console\Command;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class PruneStaleCachedPodcastContent extends Command
{
    use TenantAwareCommand;
    use HasATenantsOption;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-stale-cached-podcast-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull podcast content from the various services and cache it in database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->getTenants()->each(function ($tenant) {
            $this->info('Pruning stale cached podcast content for: ' . $tenant->id);

            if (PodcastCache::query()->count()) {
                $this->info(sprintf('Only one entry found for tenant %s Skipping', $tenant->id));
                return;
            }

            $latestEntry = PodcastCache::query()->latest()->limit(1)->first();
            if ($latestEntry === null) {
                $this->error(sprintf('No cached podcast content found for tenant: %s', $tenant->id));
                return;
            }

            $this->info(sprintf('Deleting stale cached podcast content for: %s', $tenant->id));
            PodcastCache::query()->where('id', '<>', $latestEntry->id)->delete();

            $this->info('Stale podcast content pruned successfully.');
        });
    }
}
