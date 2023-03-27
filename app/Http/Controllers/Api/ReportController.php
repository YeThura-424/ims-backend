<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getSaleProductsReport(Request $request)
    {
        $orderno = $request->orderno;
        $productsku = $request->productsku;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;

        $query = DB::table('saledetails')
            ->join('products', 'saledetails.product_id', '=', 'products.id')
            ->join('sale_invoices', 'saledetails.saleinvoice_id', '=', 'sale_invoices.id')
            ->select(
                'sale_invoices.saledate',
                'sale_invoices.orderno',
                'products.sku',
                'products.code',
                'saledetails.qty',
                'saledetails.rate',
                'saledetails.productamount',


            )
            ->groupBy(
                'sale_invoices.saledate',
                'sale_invoices.orderno',
                'products.sku',
                'products.code',
                'saledetails.qty',
                'saledetails.rate',
                'saledetails.productamount'
            );

        if (isset($orderno)) {
            $query->where('sale_invoices.orderno', $orderno);
        }
        if (isset($productsku)) {
            $query->where('products.sku', $productsku);
        }
        if (isset($fromDate) && isset($toDate)) {
            $query->whereBetween('saledetails.created_at', [$fromDate, $toDate]);
        }

        $saleProducts = $query->get();
        if ($saleProducts->isEmpty()) {
            $status = 404;
            $message = 'No Sale Products for the Selected Cliteria';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
                'data' => []
            ];

            return response()->json($response);
        } else {
            $message = 'Sales Product retrieved successfully.';
            $status = 200;

            $response = [
                'status' => $status,
                'success' => true,
                'message' => $message,
                'data' => $saleProducts,
            ];

            return response()->json($response);
        }
    }
}
