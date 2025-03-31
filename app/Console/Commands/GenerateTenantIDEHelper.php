<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateTenantIDEHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenancy:ide-helper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate IDE Helper for tenant databases';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tenant = Tenant::query()->first();
        if (!$tenant) {
            $this->info('No tenants found. Exiting.');
            return;
        }

        $this->info('Generating IDE Helper for the default tenant.');
        tenancy()->initialize($tenant);
        Artisan::call('ide-helper:generate');
        Artisan::call('ide-helper:meta');
        Artisan::call('ide-helper:models -W');
        tenancy()->end();
        $this->info('IDE Helper generation completed.');
    }
}
