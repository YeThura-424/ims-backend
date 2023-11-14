<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleInvoice;
use Illuminate\Support\Facades\DB;
use App\Models\ProductTransection;
use App\Http\Resources\ProductTransectionResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;

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
        if($saleProducts->isEmpty()) {
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

    public function getStockMovementReport(Request $request)
    {

        $type = $request->type;
        $productid = $request->productid;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;


        $movement = ProductTransection::query();

        if (isset($type)) {
            $movement->where('type', $type);
        }
        if (isset($productid)) {
            $movement->where('product_id', $productid);
        }
        if (isset($fromDate) && isset($toDate)) {
            $movement->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $stockMovement = $movement->get();

        if ($stockMovement->isEmpty()) {
            $status = 404;
            $message = 'No Transection for the Selected Cliteria';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
                'data' => []
            ];

            return response()->json($response);
        } else {
            $result = ProductTransectionResource::collection($stockMovement);

            $message = 'Stock Movement retrieved successfully.';
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

    public function getStockSummaryReport(Request $request)
    {
        $code = $request->code;
        $productid = $request->productid;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;


        $summary = Product::query();

        if (isset($code)) {
            $summary->where('code', $code);
        }
        if (isset($productid)) {
            $summary->where('id', $productid);
        }
        if (isset($fromDate) && isset($toDate)) {
            $summary->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $stockSummary = $summary->get();

        if ($stockSummary->isEmpty()) {
            $status = 404;
            $message = 'Not Found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
                'data' => 'No Stock Summary is Found for the Selected Cliteria'
            ];

            return response()->json($response);
        } else {
            $result = ProductResource::collection($stockSummary);

            $message = 'Stock Summary retrieved successfully.';
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
}
