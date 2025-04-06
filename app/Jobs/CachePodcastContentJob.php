<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use App\Repositories\PodcastCacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CachePodcastContentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Tenant $tenant)
    {
    }

    public function handle(PodcastCacheRepository $podcastCacheRepository): void
    {
        tenancy()->initialize($this->tenant);

        Log::withContext(['tenant' => $this->tenant->id]);
        Log::info(sprintf('Caching podcast content for tenant: %s', $this->tenant->id));

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->tenant->basic_auth_string,
        ])->get($this->tenant->podcast_url);

        if ($response->failed()) {
            Log::info(sprintf('Failed to fetch podcast content for tenant: %s', $this->tenant->id), [
                'response' => $response->body(),
            ]);
            return;
        }

        if (empty($response->body())) {
            Log::info(sprintf('Empty response for tenant: %s', $this->tenant->id), [
                'response' => $response->body(),
            ]);
            return;
        }

        // validate xml to be sure it is a valid xml
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body());
        if ($xml === false) {
            Log::info(sprintf('Invalid XML response for tenant: %s', $this->tenant->id), [
                'response' => $response->body(),
            ]);
            return;
        }

        $podcastCacheRepository->addPodcastCache($response->body());

        Log::info(sprintf('Podcast content cached successfully for tenant: %s', $this->tenant->id));
        tenancy()->end();
    }
}
