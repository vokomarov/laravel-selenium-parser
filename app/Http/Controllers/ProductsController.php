<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Product;

class ProductsController extends Controller
{
    /**
     * Show the all products
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return ProductResource::collection(Product::paginate());
    }

    /**
     * Show specific product
     *
     * @param  \App\Product  $product
     * @return \App\Http\Resources\ProductResource
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}
