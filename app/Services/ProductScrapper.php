<?php

namespace App\Services;

use Illuminate\Support\Collection;

interface ProductScrapper
{
    /**
     * Go to given category
     *
     * @param  string  $url
     * @return void
     */
    public function selectCategory(string $url): void;

    /**
     * Go to given product page
     *
     * @param  string  $url
     * @return void
     */
    public function selectProduct(string $url): void;

    /**
     * Parse all available products on current page
     *
     * @return \Illuminate\Support\Collection<\App\Services\RemoteProduct>
     */
    public function parseProducts(): Collection;

    /**
     * Parse full product details on current page
     *
     * @return \App\Services\RemoteProduct
     */
    public function parseFullProduct(): RemoteProduct;
}
