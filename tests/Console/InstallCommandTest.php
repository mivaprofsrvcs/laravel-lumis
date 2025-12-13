<?php

use Illuminate\Support\Facades\File;

/** @var \Tests\TestCase $this */

beforeEach(function (): void {
    /** @var \Tests\TestCase $this */
    $this->configPath = config_path('miva.php');

    if (File::exists($this->configPath)) {
        File::delete($this->configPath);
    }
});

afterEach(function (): void {
    /** @var \Tests\TestCase $this */
    if (File::exists($this->configPath)) {
        File::delete($this->configPath);
    }
});

it('registers the lumis:install command', function (): void {
    /** @var \Tests\TestCase $this */
    $this->artisan('list')
        ->expectsOutputToContain('lumis:install')
        ->assertExitCode(0);
});

it('supports the --no-env option', function (): void {
    /** @var \Tests\TestCase $this */
    $this->artisan('lumis:install --no-env')->assertExitCode(0);
});

it('prints env example by default', function (): void {
    /** @var \Tests\TestCase $this */
    $this->artisan('lumis:install --no-next')
        ->expectsOutputToContain('Add the following to your .env')
        ->expectsOutputToContain('MM_STORE_URL=https://example.test')
        ->expectsOutputToContain('MM_API_URL="${MM_STORE_URL}${MM_STORE_ROOT_PATH}json.mvc"')
        ->assertExitCode(0);
});

it('prints next steps by default', function (): void {
    /** @var \Tests\TestCase $this */
    $this->artisan('lumis:install --no-env')
        ->expectsOutputToContain('Next steps')
        ->assertExitCode(0);
});

it('supports the --no-next option', function (): void {
    /** @var \Tests\TestCase $this */
    $this->artisan('lumis:install --no-env --no-next')
        ->doesntExpectOutputToContain('Next steps')
        ->assertExitCode(0);
});

it('publishes the config file successfully', function (): void {
    /** @var \Tests\TestCase $this */
    $this->artisan('lumis:install --no-env')->assertExitCode(0);

    expect(File::exists($this->configPath))->toBeTrue();
});

it('supports the --force option and still publishes config', function (): void {
    /** @var \Tests\TestCase $this */
    // Seed a dummy file to ensure overwrite works.
    File::put($this->configPath, '<?php return [];');

    $this->artisan('lumis:install --force --no-env')->assertExitCode(0);

    expect(File::exists($this->configPath))->toBeTrue();
});
