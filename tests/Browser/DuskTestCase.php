<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use App\Models\User;

abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected $connectionsToTransact = [];

    /**
     * Fresh schema per browser test without the rollback cycle —
     * legacy migration down() paths are unreliable, and browser
     * requests need committed data anyway.
     */
    protected function runDatabaseMigrations(): void
    {
        $this->artisan('migrate:fresh', ['--seed' => true]);
        $this->app[\Illuminate\Contracts\Console\Kernel::class]->setArtisan(null);
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions())->addArguments([
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1920,1080',
        ]);

        $capabilities = DesiredCapabilities::chrome()
            ->setCapability(ChromeOptions::CAPABILITY, $options);

        return RemoteWebDriver::create(
            $this->wdServerUrl(),
            $capabilities,
            10000,
            300000
        );
    }

    protected function wdServerUrl(): string
    {
        return env('DUSK_SERVER_URL', 'http://127.0.0.1:9515');
    }

    public function browse(\Closure $callback)
    {
        return parent::browse(function (...$browsers) use ($callback) {
            collect($browsers)->each(fn ($browser) => $browser->driver->manage()->deleteAllCookies());

            $callback(...$browsers);
        });
    }

    protected function loginAsAdmin($browser): void
    {
        $user = User::where('email', 'admin@brickspoint.ng')->first();

        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password');

        try {
            $browser->press('#login-btn');
        } catch (\Facebook\WebDriver\Exception\WebDriverException $e) {
            usleep(800000);
            $browser->press('#login-btn');
        }

        $browser->waitUntil(
            'window.location.pathname.startsWith("/admin") || window.location.pathname === "/dashboard"',
            30
        );
    }
}

