<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ImportToWarehouseController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductConversionController;
use App\Http\Controllers\Api\ProductPricingController;
use App\Http\Controllers\Api\SaleInvoiceController;
use App\Http\Controllers\Api\VendorController;
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


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiresource('categories', CategoryController::class);
    Route::apiresource('brands', BrandController::class);
    Route::apiresource('products', ProductController::class);
    Route::apiresource('vendors', VendorController::class);
    Route::apiresource('importtowarehouses', ImportToWarehouseController::class);
    Route::apiresource('saleinvoice', SaleInvoiceController::class);
    Route::apiresource('productconversion', ProductConversionController::class);
    Route::apiresource('productpricing', ProductPricingController::class);
    Route::get('/minimumproduct', [DashboardController::class, 'getMinimumProductList']);
    Route::get('/todaysale', [DashboardController::class, 'getTodaySales']);
});



Route::post('/login', [AuthController::class, 'login']);
