<?php

namespace MVPS\Lumis\Providers;

use Illuminate\Support\ServiceProvider;
use MVPS\Lumis\Console\InstallCommand;

class LumisServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath('miva.php'), 'miva');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            $this->configPath('miva.php') => config_path('miva.php'),
        ], 'lumis-config');

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
        }
    }

    /**
     * Resolve the absolute path to a configuration file within the package.
     */
    private function configPath(string $path): string
    {
        return __DIR__ . '/../../config/' . $path;
    }
}
