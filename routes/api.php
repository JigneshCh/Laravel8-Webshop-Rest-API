<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\OrderController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('orders/{id}/pay', [OrderController::class, 'pay'])->name('pay');
Route::post('orders/{id}/add', [OrderController::class, 'addProduct'])->name('addProduct');
Route::delete('orders/{orderId}/product/{productId}', [OrderController::class, 'removeProduct'])->name('removeProduct');
Route::resource('orders', OrderController::class);





















