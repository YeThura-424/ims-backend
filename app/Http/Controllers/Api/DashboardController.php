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
}
