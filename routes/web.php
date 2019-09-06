<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/order-api', function () {
    return view('swagger');
});

// Route::get('orders', 'OrderController@getAllOrder');
// Route::post('orders', 'OrderController@store');
// Route::middleware(ValidateId::class)->patch('orders/{id}', 'OrderController@patchOrderStatus');
