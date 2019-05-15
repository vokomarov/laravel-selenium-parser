<?php

namespace App\Services\Scrapper;

use App\Services\ProductScrapper;
use App\Services\RemoteProduct;
use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CoolBlueScrapper implements ProductScrapper
{
    const BASE_URL = 'https://www.coolblue.nl';

    /**
     * @var \Facebook\WebDriver\WebDriver
     */
    protected $driver;

    /**
     * CoolBlueScrapper constructor.
     *
     * @param \Facebook\WebDriver\WebDriver $driver
     */
    public function __construct(WebDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param  string  $url
     * @return string
     */
    protected function url(string $url): string
    {
        return self::BASE_URL . $url;
    }

    /**
     * @param string $url
     */
    public function selectCategory(string $url): void
    {
        $this->driver->navigate()->to($this->url($url));
    }

    /**
     * @return \Illuminate\Support\Collection<\App\Services\RemoteProduct>
     */
    public function parseProducts(): Collection
    {
        $products = collect();

        $elements = [];

        try {
            $elements = $this->driver->findElements(WebDriverBy::cssSelector(".product-grid .product"));
        } catch (Exception $exception) {
            //
        }

        foreach ($elements as $element) {
            try {
                $titleElement = $element->findElement(WebDriverBy::className('product__title'));

                $products->push(new RemoteProduct([
                    'name' => $titleElement->getText(),
                    'url' => $titleElement->getAttribute('href'),
                ]));
            } catch (Exception $exception) {
                //
            }
        }
        
        return $products;
    }

    /**
     * Go to selected product and wait before pare is completely loaded
     *
     * @param  string  $url
     * @return void
     */
    public function selectProduct(string $url): void
    {
        $this->driver->navigate()->to($url);

        try {
            $this->driver
                ->wait()
                ->until(
                    WebDriverExpectedCondition::presenceOfElementLocated(
                        WebDriverBy::cssSelector('.js-product-description-content')
                    )
                )->getText();
        } catch (Exception $exception) {
            //
        }
    }

    /**
     * Fetch full product information from current page
     *
     * @return \App\Services\RemoteProduct
     */
    public function parseFullProduct(): RemoteProduct
    {
        $product = new RemoteProduct();

        try {
            $product->name = $this->driver
                ->findElement(WebDriverBy::className('js-product-name'))
                ->getText();
        } catch (Exception $exception) {
            //
        }

        try {
            $product->description = $this->driver
                ->findElement(
                    WebDriverBy::cssSelector('.js-product-description-content')
                )
                ->getText();
        } catch (Exception $exception) {
            //
        }

        try {
            $product->rating = $this->parseRating(
                $this->driver
                    ->findElement(WebDriverBy::className('review-rating__icons'))
                    ->getText()
            );
        } catch (Exception $exception) {
            //
        }

        try {
            $images = $this->driver->findElements(
                WebDriverBy::cssSelector('.product-media-gallery__wrapper li img')
            );

            $image = Arr::first($images);

            $product->imageUrl = $image->getAttribute('src');
        } catch (Exception $exception) {
            //
        }

        try {
            $product->price = $this->parsePrice(
                $this->driver
                    ->findElement(WebDriverBy::className('sales-price__current'))
                    ->getText()
            );
        } catch (Exception $exception) {
            //
        }

        return $product;
    }

    /**
     * Extract rating value from sentence about product rating
     *
     * @param  string  $sentence
     * @return float
     */
    protected function parseRating(string $sentence): float
    {
        $match = [];

        preg_match('/([0-5]{1})(\.)?([0-9]{1,2})?/', $sentence, $match);

        if (count($match) < 1) {
            return 0.0;
        }

        return (float) $match[0] ?? 0.0;
    }

    /**
     * Format remote product price from string to proper float value
     *
     * @param  string  $price
     * @return float
     */
    protected function parsePrice(string $price): float
    {
        return (float) str_replace(',', '.', str_replace('.', '', $price));
    }
}
