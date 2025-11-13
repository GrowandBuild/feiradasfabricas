<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    // Shipping quote endpoint (Phase 1)
Route::post('/shipping/quote', [\App\Http\Controllers\Api\ShippingController::class, 'quote'])->name('api.shipping.quote');
Route::get('/shipping/track/{provider}/{code}', [\App\Http\Controllers\Api\ShippingController::class, 'track'])->name('api.shipping.track');
Route::get('/address/{cep}', [\App\Http\Controllers\Api\ShippingController::class, 'addressLookup'])->name('api.address.lookup');
Route::post('/shipping/select', [\App\Http\Controllers\Api\ShippingController::class, 'select'])->name('api.shipping.select');
Route::get('/shipping/selection', [\App\Http\Controllers\Api\ShippingController::class, 'currentSelection'])->name('api.shipping.selection');
Route::get('/cart/summary', [\App\Http\Controllers\Api\ShippingController::class, 'summary'])->name('api.cart.summary');
