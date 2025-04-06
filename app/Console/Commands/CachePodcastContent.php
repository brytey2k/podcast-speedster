<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Traits\SendsConsoleOutputToLogs;
use App\Jobs\CachePodcastContentJob;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class CachePodcastContent extends Command
{
    use TenantAwareCommand;
    use HasATenantsOption;
    use SendsConsoleOutputToLogs;

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
        $self = $this;
        $this->getTenants()->each(static function (Tenant $tenant) use ($self) {
            Log::withContext(['tenant' => $tenant->id]);
            $self->infoConsoleOutputAndLog(sprintf('Dispatching CachePodcastContentJob for tenant: %s', $tenant->id));

            dispatch(new CachePodcastContentJob($tenant));

            $self->infoConsoleOutputAndLog(sprintf('CachePodcastContentJob dispatched for tenant: %s', $tenant->id));
        });
    }
}
