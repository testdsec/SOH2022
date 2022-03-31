<?php

use Illuminate\Support\Facades\Route;


Route::get('/', 'IndexController')->name('index');

Route::get('/cart/index', 'CartController@index')->name('index_cart');
Route::get('/cart/checkout', 'CartController@checkout')->name('checkout');
Route::post('/cart/saveorder', 'CartController@saveOrder')->name('saveorder');
Route::post('/cart/add/{id}', 'CartController@add')->where('id', '[0-9]+')->name('add');
Route::post('/cart/add_item/{id}', 'CartController@add_item')->where('id', '[0-9]+')->nae('add_item');
Route::post('/cart/reduce/{id}', 'CartController@reduce')->where('id', '[0-9]+')->name('reduce');
Route::post('/cart/remove/{id}', 'CartController@remove')->where('id', '[0-9]+')->name('remove');
Route::post('/cart/clear', 'CartController@clear')->name('clear');

Route::get('/orders', 'OrderController@index')->name('orders');
Route::get('/order/finish', 'OrderController@finish')->name('order_finish');
Route::get('/order/{order}', 'OrderController@show')->name('order');
Route::post('/order/invoice/download', 'OrderController@download_invoice')->name('download_bill');
Route::post('/order/update', 'OrderController@index')->name('order_update');



