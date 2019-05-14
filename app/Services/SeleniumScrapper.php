<?php

namespace App\Services;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class SeleniumScrapper
{
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected $client;

    /**
     * SeleniumScrapper constructor.
     */
    public function __construct()
    {
        $host = config('services.selenium.chrome.host');
        $port = config('services.selenium.chrome.port');

        $caps = DesiredCapabilities::chrome();

        $this->client = RemoteWebDriver::create("{$host}:{$port}/wd/hub", $caps);
    }

    /**
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function getClient(): RemoteWebDriver
    {
        return $this->client;
    }
}
