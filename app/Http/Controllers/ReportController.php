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

    public function generateStockSummaryReport()
    {
        // $stockSummary = DB::table('importdetails')
        //     ->join('products', 'products.id', '=', 'importdetails.product_id')
        //     ->join('import_to_warehouses', 'import_to_warehouses.id', '=', 'importdetails.importtowarehouse_id')
        //     ->leftJoin('saledetails', 'saledetails.product_id', '=', 'importdetails.product_id')
        //     ->select(
        //         'import_to_warehouses.warehouse as Warehouse',
        //         'products.name as ProductName',
        //         'products.code as ProductCode',
        //         DB::raw('(COALESCE(SUM(importdetails.qty), 0) - COALESCE(SUM(saledetails.qty), 0)) as ClosingQty'),
        //         DB::raw('(COALESCE(SUM(importdetails.qty),0)) as ImportedQty'),
        //         DB::raw('(COALESCE(SUM(saledetails.qty), 0)) as SaleQty'),
        //         // 'importdetails.rate as PurchaseRate'
        //     )

        //     ->where('import_to_warehouses.warehouse', 'defaultwarehouse')
        //     ->groupBy('import_to_warehouses.warehouse', 'products.name', 'products.code')
        //     // ->orderBy('import_to_warehouses.warehouse')
        //     // ->orderBy('products.name')
        //     ->get();

        $stockSummary = DB::table('importdetails')
            ->join('products', 'importdetails.product_id', '=', 'products.id')
            ->join('import_to_warehouses', 'importdetails.importtowarehouse_id', '=', 'import_to_warehouses.id')
            ->select(
                'products.name',
                'products.code',
                'import_to_warehouses.warehouse',
                DB::raw('(SUM(importdetails.qty)) as ImportedQty'),

            )
            ->where('import_to_warehouses.warehouse', '=', 'defaultwarehouse')
            ->groupBy('import_to_warehouses.warehouse', 'products.name', 'products.code')
            ->get();
        $saleSummary = DB::table('saledetails')
            ->join('products', 'saledetails.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.code',
                DB::raw('(SUM(saledetails.qty)) as SaleQty')
            )
            ->groupBy('products.name', 'products.code')
            ->get();



        $combinedData = [];
        foreach ($stockSummary as $stock) {
            foreach ($saleSummary as $sale) {
                if ($stock->code == $sale->code) {
                    $combinedData[] = [
                        'warehouse' => $stock->warehouse,
                        'product_name' => $sale->name,
                        'product_code' => $sale->code,
                        'importedQty' => $stock->ImportedQty,
                        'saleQty' => $sale->SaleQty,
                        'closingQty' => $stock->ImportedQty - $sale->SaleQty
                    ];
                }
            }
        }

        $response = [
            // 'stock' => $stockSummary,
            // 'sale' => $saleSummary,
            'data' => $combinedData
        ];
        return response()->json($response);
    }

    public function stockSummaryGenerate(Request $request)
    {


        // dd($request->warehouse);
        $warehouse = $request->warehouse;
        $query = DB::table('importdetails');

        if (isset($warehouse)) {
            $query->where('importdetails.warehouse', $warehouse);
        }

        $importedStock = $query->get();

        return $importedStock;
    }
}
