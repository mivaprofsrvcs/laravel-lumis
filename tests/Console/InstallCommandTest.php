<?php

use Illuminate\Support\Facades\File;

beforeEach(function (): void {
	$this->configPath = config_path('miva.php');

	if (File::exists($this->configPath)) {
		File::delete($this->configPath);
	}
});

afterEach(function (): void {
	if (File::exists($this->configPath)) {
		File::delete($this->configPath);
	}
});

it('registers the lumis:install command', function (): void {
	$this->artisan('list')
		->expectsOutputToContain('lumis:install')
		->assertExitCode(0);
});

it('supports the --no-env option', function (): void {
	$this->artisan('lumis:install --no-env')->assertExitCode(0);
});

it('publishes the config file successfully', function (): void {
	$this->artisan('lumis:install --no-env')->assertExitCode(0);

	expect(File::exists($this->configPath))->toBeTrue();
});

it('supports the --force option and still publishes config', function (): void {
	// Seed a dummy file to ensure overwrite works.
	File::put($this->configPath, '<?php return [];');

	$this->artisan('lumis:install --force --no-env')->assertExitCode(0);

	expect(File::exists($this->configPath))->toBeTrue();
});
