<?php

namespace App\Console\Commands;

use App\Product;
use App\Services\ScrapperFactory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:scrap {category : Url of category started with slash}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrap products from coolblue.nl';

    /**
     * @var \App\Services\ProductScrapper
     */
    protected $scrapper;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\ScrapperFactory $factory
     */
    public function __construct(ScrapperFactory $factory)
    {
        parent::__construct();

        $this->scrapper = $factory->getScrapper();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $category = $this->argument('category');

        if (empty($category)) {
            $this->error("Please, specify category to scrap products first.");

            return;
        }

        $this->info("Scrapping category {$category}");
        $this->scrapper->selectCategory($category);

        $products = $this->scrapper->parseProducts();
        $foundProducts = count($products);
        $this->info("Found {$foundProducts} products.");

        $progress = 0;

        foreach ($products as $product) {
            try {
                $this->info("Scrapping {$product->name}..");

                $this->scrapper->selectProduct($product->url);
                $fullProduct = $this->scrapper->parseFullProduct();

                $product = Product::updateOrCreate([
                    'name' => $fullProduct->name,
                    'imageUrl' => $fullProduct->imageUrl,
                ]);

                $product->price = $fullProduct->price;
                $product->description = $fullProduct->description;
                $product->rating = $fullProduct->rating;

                $product->save();
            } catch (Exception $exception) {
                Log::warning("Unable to parse or save product", [
                    'url' => $product->url,
                    'name' => $product->name,
                    'message' => $exception->getMessage(),
                ]);
            }

            $progress++;
        }

        $this->info("Done.");
    }
}
