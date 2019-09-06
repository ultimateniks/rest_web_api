<?php

use Illuminate\Http\Request;

use App\Http\Middleware\ValidateId;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('orders', 'OrderController@getAllOrder');
Route::post('orders', 'OrderController@store');
Route::middleware(ValidateId::class)->patch('orders/{id}', 'OrderController@patchOrderStatus');


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
