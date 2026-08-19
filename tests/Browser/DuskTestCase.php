<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected function driver(): RemoteWebDriver
    {
        $options = (new \Facebook\WebDriver\Chrome\ChromeOptions())->addArguments([
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
            $capabilities
        );
    }

    protected function wdServerUrl(): string
    {
        return env('DUSK_SERVER_URL', 'http://localhost:9515');
    }
}
