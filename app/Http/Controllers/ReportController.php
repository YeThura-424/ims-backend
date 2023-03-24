<?php

namespace App\Http\Controllers;

use App\Models\ImportToWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ReportController extends Controller
{
    public function getStockSummaryReport(Request $request)
    {
        $warehouse = $request->warehouse;
        $productName = $request->productname;
        $productCode = $request->code;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        // dd($warehouse);
        $query = DB::table('importdetails')
            ->join('import_to_warehouses', 'importdetails.importtowarehouse_id', '=', 'import_to_warehouses.id')
            ->join('products', 'importdetails.product_id', '=', 'products.id')
            ->select(
                'import_to_warehouses.warehouse',
                'products.name',
                'products.code',
                DB::raw('SUM(importdetails.qty) AS closing_qty'),
                // DB::raw('(importdetails.rate) AS purchase_rate')
            )
            ->groupBy('import_to_warehouses.warehouse', 'products.name', 'products.code');

        if (isset($warehouse)) {
            $query->where('import_to_warehouses.warehouse', $warehouse);
        }
        if (isset($productName)) {
            $query->where('products.name', $productName);
        }
        if (isset($fromDate) && isset($toDate)) {
            $query->whereBetween('importdetails.created_at', [$fromDate, $toDate]);
        }
        $stockSummary = $query->get();

        return $stockSummary;
    }


    public function getNewStockSummaryReport()
    {
        $stockSummary = DB::table('products')
            ->leftJoin(DB::raw('(SELECT importtowarehouse_id, product_id, SUM(qty) AS total_quantity FROM importdetails GROUP BY importtowarehouse_id, product_id) AS i'), 'products.id', '=', 'i.product_id')
            ->leftJoin(DB::raw('(SELECT saleinvoice_id, product_id, SUM(qty) AS total_quantity FROM saledetails GROUP BY saleinvoice_id, product_id) AS s'), 'products.id', '=', 's.product_id')
            ->select('products.id', 'products.name', DB::raw('(COALESCE(i.total_quantity, 0) - COALESCE(s.total_quantity, 0)) AS remaining_quantity'))
            ->get();

        return $stockSummary;
    }
}
