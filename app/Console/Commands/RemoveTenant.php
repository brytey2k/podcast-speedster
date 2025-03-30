<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Stancl\Tenancy\Contracts\Domain;
use Symfony\Component\Console\Input\InputOption;

class RemoveTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a tenant with its domain and database';

    protected function configure(): void
    {
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'String to use as id and subdomain for tenant');
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $id = $this->option('id');

        if (! $id) {
            $this->error('An ID needs to be specified. Pass this by using the --id option');

            return;
        }

        $tenant = Tenant::find($id);
        if (! $tenant) {
            $this->error('Tenant does not exist');

            return;
        }

        // delete the domains of tenant
        $this->info('Tenant Domains deleted:');
        $tenant->domains->each(function (Domain $domain) {
            $domain->delete();
            $this->info('  ' . $domain->domain);
        });

        // delete the tenant
        $tenant->delete();
        $this->info('Tenant ID: ' . $tenant->id . ' deleted');

        // drop tenant database using laravel database manager
        if ($tenant->database()->manager()->databaseExists($tenant->tenancy_db_name)) {
            $tenant->database()->manager()->deleteDatabase($tenant);
        }
    }
}
