<?php

namespace App\Console\Commands;

use App\Product;
use App\Services\SeleniumScrapper;
use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ScrapProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:scrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrap products from selected source';

    /**
     * @var \App\Services\SeleniumScrapper
     */
    protected $scrapper;

    /**
     * @var array
     */
    protected $products = [];

    /**
     * Create a new command instance.
     *
     * @param \App\Services\SeleniumScrapper $scrapper
     */
    public function __construct(SeleniumScrapper $scrapper)
    {
        parent::__construct();

        $this->scrapper = $scrapper;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->scrapper->getClient()->navigate()->to('https://www.coolblue.nl/laptops');
        $elements = $this->scrapper->getClient()->findElements(WebDriverBy::cssSelector(".product-grid .product"));

        $amount = 0;

        foreach ($elements as $element) {
            try {
                $titleElement = $element->findElement(WebDriverBy::className('product__title'));

                $this->products[] = [
                    'name' => $titleElement->getText(),
                    'url' => $titleElement->getAttribute('href'),
                ];
            } catch (Exception $exception) {
                $this->error($exception->getMessage());

                continue;
            }
        }

        foreach ($this->products as ['name' => $name, 'url' => $url]) {
            try {
                if ($this->scrapProduct($name, $url)) {
                    $amount++;
                }
            } catch (Exception $exception) {
                continue;
            }
        }

        $this->info("Added or updated {$amount} products.");
    }

    /**
     * @param  string  $name
     * @param  string  $url
     * @return bool
     */
    protected function scrapProduct(string $name, string $url): bool
    {
        $this->info("Scrapping {$name}");

        $this->scrapper->getClient()->navigate()->to($url);

        $productDescription = null;

        try {
            $productDescription = $this->scrapper
                ->getClient()
                ->wait()
                ->until(
                    WebDriverExpectedCondition::presenceOfElementLocated(
                        WebDriverBy::cssSelector('.js-product-description-content')
                    )
                )->getText();
        } catch (NoSuchElementException | TimeOutException | Exception $exception) {
            Log::warning("Unable to find description of product.", [
                'url' => $url,
                'name' => $name,
                'message' => $exception->getMessage(),
            ]);
        }

        $productRating = $this->parseRating(
            $this->scrapper
                ->getClient()
                ->findElement(WebDriverBy::className('review-rating__icons'))
                ->getAttribute('title')
        );
        
        $images = $this->scrapper
            ->getClient()
            ->findElements(WebDriverBy::cssSelector('.product-media-gallery__wrapper li img'));

        $image = Arr::first($images);

        $productImageUrl = $image->getAttribute('src');

        $productPrice = $this->formatPrice(
            $this->scrapper
                ->getClient()
                ->findElement(WebDriverBy::className('sales-price__current'))
                ->getText()
        );

        try {
            $product = Product::updateOrCreate([
                'name' => $name,
                'imageUrl' => $productImageUrl,
            ]);

            $product->price = $productPrice;
            $product->description = $productDescription;
            $product->rating = $productRating;

            $product->save();
        } catch (Exception $exception) {
            Log::warning("Unable to save product", [
                'url' => $url,
                'name' => $name,
                'message' => $exception->getMessage(),
            ]);
        }

        return true;
    }

    /**
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
     * @param  string  $price
     * @return float
     */
    protected function formatPrice(string $price): float
    {
        return (float) str_replace(',', '.', str_replace('.', '', $price));
    }
}
