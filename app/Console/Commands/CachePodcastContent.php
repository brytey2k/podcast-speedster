<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PodcastCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class CachePodcastContent extends Command
{
    use TenantAwareCommand;
    use HasATenantsOption;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache-podcast-content';

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
            $this->info('Caching podcast content...' . $tenant->id);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $tenant->basic_auth_string,
            ])->get($tenant->podcast_url);

            if ($response->failed()) {
                $this->error('Failed to fetch podcast content for tenant: ' . $tenant->id);
                return;
            }

            PodcastCache::query()->create([
                'content' => $response->body(),
            ]);

            $this->info('Podcast content cached successfully.');
        });
    }
}
