<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a tenant with its domain and database';

    protected function configure(): void
    {
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'String to use as id and subdomain for tenant');
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'Tenant name');
        $this->addOption('podcast_username', null, InputOption::VALUE_OPTIONAL, 'Podcast username');
        $this->addOption('podcast_password', null, InputOption::VALUE_OPTIONAL, 'Podcast password');
        $this->addOption('podcast_url', null, InputOption::VALUE_OPTIONAL, 'Podcast URL');
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $id = $this->option('id');
        $name = $this->option('name');
        $username = $this->option('podcast_username');
        $password = $this->option('podcast_password');
        $podcastUrl = $this->option('podcast_url');

        if (! $id) {
            $this->error('An ID needs to be specified. Pass this by using the --id option');

            return;
        }

        if (! $name) {
            $this->error('A name needs to be specified. Pass this by using the --name option');

            return;
        }

        if (! $podcastUrl) {
            $this->error('A podcast URL needs to be specified. Pass this by using the --podcast_url option');

            return;
        }

        if (Tenant::query()->where('id', '=', $id)->exists()) {
            $this->error('Another tenant with this id already exists');

            return;
        }

        $tenant = Tenant::query()->create([
            'id' => $id,
            'api_key' => '',
            'sender_id' => $id,
            'name' => $name,
            'basic_auth_string' => base64_encode(
                sprintf('%s:%s', $username, $password)
            ),
            'podcast_url' => $podcastUrl,
        ]);
        $tenant->domains()->create(['domain' => "$id." . parse_url(config('app.url'), PHP_URL_HOST)]);

        $this->info('Tenant ID: ' . $tenant->id);
        $this->info('Tenant Domains:');
        $tenant->domains->each(function ($domain) {
            $this->info('  ' . $domain->domain);
        });
        $this->info('Tenant database: ' . $tenant->tenancy_db_name);
    }
}
