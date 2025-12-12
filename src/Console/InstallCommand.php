<?php

namespace MVPS\Lumis\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'lumis:install', description: 'Install and configure the Lumis package')]
class InstallCommand extends Command
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $signature = 'lumis:install
        {--force : Overwrite existing files}
        {--no-env : Do not print the .env example block}
        {--no-next : Do not print the Next Steps section}';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $description = 'Install and configure the Lumis package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->publishConfig();

        if (! $this->option('no-env')) {
            $this->printEnvExample();
        }

        if (! $this->option('no-next')) {
            $this->printNextSteps();
        }

        return self::SUCCESS;
    }

    /**
     * Publish the package configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('Publishing configuration file (config/miva.php)...');

        $this->call('vendor:publish', [
            '--provider' => 'MVPS\\Lumis\\Providers\\LumisServiceProvider',
            '--tag' => 'lumis-config',
            '--force' => (bool) $this->option('force'),
        ]);

        $this->newLine();

        $this->components->info('Configuration published');
    }

    /**
     * Output example environment variables users can copy to their .env file.
     */
    protected function printEnvExample(): void
    {
        $this->newLine();

        $this->components->twoColumnDetail('Add the following to your .env', '(example)');

        $this->newLine();

        $lines = [
            'MM_STORE_URL=https://example.test',
            'MM_STORE_CODE=s01',
            'MM_STORE_ROOT_PATH=/mm5/',
            'MM_STORE_GRAPHICS_PATH=graphics/00000001/',
            '',
            'MM_STORE_AUTH_USERNAME=',
            'MM_STORE_AUTH_PASSWORD=',
            '',
            'MM_API_KEY=api-signature-key',
            'MM_API_TOKEN=api-access-token',
            'MM_API_URL="${MM_STORE_URL}${MM_STORE_ROOT_PATH}json.mvc"',
            'MM_API_VERIFY_SSL=false',
        ];

        foreach ($lines as $line) {
            $this->line($line);
        }

        $this->newLine();
    }

    /**
     * Print next recommended steps after installation.
     */
    protected function printNextSteps(): void
    {
        $this->components->info('Next steps');

        $this->components->bulletList([
            'Review and customize config/miva.php',
            'Add your Miva credentials to the .env file',
            'Define multiple connections (config/miva.php → miva.connections)',
            'Set the default connection via MM_CONNECTION',
            'Run: php artisan config:cache',
        ]);
    }
}
