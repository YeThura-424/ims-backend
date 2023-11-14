<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Models\SaleInvoice;
use App\Http\Resources\SaleInvoiceResource;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getMinimumProductList()
    {
        $minimum_products = Product::where('qty', '<', 6)->get();
        $result = ProductResource::collection($minimum_products);

        $message = 'Product retrieved successfully.';
        $status = 200;

        $response = [
            'status' => $status,
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];
        return response()->json($response);
    }


    public function getTodaySales()
    {
        $today = date('Y-m-d');
        $totay_sales = SaleInvoice::where('saledate', $today)->get();

        $result = SaleInvoiceResource::collection($totay_sales);

        $message = 'Sales retrieved successfully.';
        $status = 200;

        $response = [
            'status' => $status,
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];
        return response()->json($response);
    }

    public function getTodayImportedProducts()
    {
        $today = date('Y-m-d');
        $today_imported_product = DB::table('importdetails')
            ->join('products', 'importdetails.product_id', '=', 'products.id')
            ->join('import_to_warehouses', 'importdetails.importtowarehouse_id', '=', 'import_to_warehouses.id')
            ->select(
                'products.sku',
                'products.code',
                DB::raw('SUM(importdetails.qty) AS qty'),
            )
            ->groupBy('products.sku', 'products.code')
            ->where('import_to_warehouses.date', $today)->get();


        $message = 'Product retrieved successfully.';
        $status = 200;

        $response = [
            'status' => $status,
            'success' => true,
            'message' => $message,
            'data' => $today_imported_product,
        ];
        return response()->json($response);
    }
}
