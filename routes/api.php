<?php

Route::get('/products', 'ProductsController@index')->name('products');
Route::get('/products/{product}', 'ProductsController@show')->name('products.show');
