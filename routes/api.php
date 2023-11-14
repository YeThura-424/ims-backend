<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImportToWarehouseController;
use App\Http\Controllers\Api\SaleInvoiceController;
use App\Http\Controllers\Api\StockSummaryController;
use App\Http\Controllers\Api\ProductConversionController;
use App\Http\Controllers\Api\ProductPricingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiresource('products', ProductController::class);
    Route::apiresource('categories', CategoryController::class);
    Route::apiresource('brands', BrandController::class);
    Route::apiresource('vendors', VendorController::class);
    Route::apiresource('importtowarehouses', ImportToWarehouseController::class);
    Route::apiresource('saleinvoice', SaleInvoiceController::class);
    Route::apiresource('productconversion', ProductConversionController::class);
    Route::apiresource('productpricing', ProductPricingController::class);
    Route::get('/minimumproduct', [DashboardController::class, 'getMinimumProductList']);
    Route::get('/todaysale', [DashboardController::class, 'getTodaySales']);
    Route::get('/todayimportedproduct', [DashboardController::class, 'getTodayImportedProducts']);
    Route::post('/saleproduct', [ReportController::class, 'getSaleProductsReport']);
    Route::post('/stockmovement', [ReportController::class, 'getStockMovementReport']);
    Route::post('/stocksummary', [ReportController::class, 'getStockSummaryReport']);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/stocksummaryreport', [StockSummaryController::class, 'getStockSummaryReport']);


// Route::apiresource('productpricing', ProductPricingController::class);
// Route::get('/saleproduct', [ReportController::class, 'getSaleProductsReport']);
// Route::get('/stockmovement', [ReportController::class, 'getStockMovementReport']);
