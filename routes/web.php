<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
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

Route::get('/stocksummaryreport', [ReportController::class, 'getStockSummaryReport']);
Route::get('/newstocksummaryreport', [ReportController::class, 'getNewStockSummaryReport']);
Route::get('/generatestocksummaryreport', [ReportController::class, 'generateStockSummaryReport']);
Route::post('/stocksummary', [ReportController::class, 'stockSummaryGenerate']);
