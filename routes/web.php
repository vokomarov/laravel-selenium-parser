<?php

Auth::routes();

Route::get('/', 'WelcomeController@index')->name('welcome');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
});

