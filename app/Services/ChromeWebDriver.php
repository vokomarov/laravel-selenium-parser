<?php

namespace App\Services;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class ChromeWebDriver
{
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected $driver;

    /**
     * SeleniumScrapper constructor.
     */
    public function __construct()
    {
        $host = config('services.selenium.chrome.host');
        $port = config('services.selenium.chrome.port');

        $caps = DesiredCapabilities::chrome();

        $this->driver = RemoteWebDriver::create("{$host}:{$port}/wd/hub", $caps);
    }

    /**
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function getDriver(): RemoteWebDriver
    {
        return $this->driver;
    }
}
