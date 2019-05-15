<?php


namespace App\Services;

use App\Services\Scrapper\CoolBlueScrapper;
use Facebook\WebDriver\WebDriver;

class ScrapperFactory
{
    /**
     * @return \App\Services\ProductScrapper
     */
    public function getScrapper(): ProductScrapper
    {
        return new CoolBlueScrapper($this->getDriver());
    }

    /**
     * @return \Facebook\WebDriver\WebDriver
     */
    public function getDriver(): WebDriver
    {
        return (new ChromeWebDriver())->getDriver();
    }
}
