<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleInvoice;
use App\Http\Resources\SaleInvoiceResource;
use Validator;
use App\Models\Product;

class SaleInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sale = SaleInvoice::all();
        $result = SaleInvoiceResource::collection($sale);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'totalamount'  => ['required'],
            'saleItem' => ['required']
        ]);

        if ($validator->fails()) {
            $status = 400;
            $message = 'Validation Error';
            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
                'data' => $validator->errors(),
            ];

            return response()->json($response);
        } else {
            $saledate = $request->saledate;
            $totalamount = $request->totalamount;
            $remark = $request->remark;
            $saleItem = json_decode($request->saleItem);
            $uniqid = uniqid();
            $orderno = "EXP-" . $saledate . '-' . $uniqid;
            $warehouse = 'defaultwarehouse';
            $saleinvoice = new SaleInvoice;
            $saleinvoice->saledate = $saledate;
            $saleinvoice->orderno = $orderno;
            $saleinvoice->remark = $remark;
            $saleinvoice->totalamount = $totalamount;
            $saleinvoice->save();

            $saleinvoiceId = $saleinvoice->id;
            foreach ($saleItem as $item) {
                $id = $item->id;
                $qty = $item->saleqty;
                $rate = $item->rate;
                $amount = $item->productamount;

                $saleinvoice->products()->attach($id, [
                    'qty' => $qty,
                    'rate' => $rate,
                    'productamount' => $amount,
                ]);

                $product = Product::find($id);
                $opening = $product->qty;
                $closing = $opening - $qty;
                $product->qty = $closing;
                $product->save();
            }
            $status = 200;
            $message = 'Sale created successfully.';
            $result = new SaleInvoiceResource($saleinvoice);

            $response = [
                'success' => true,
                'status' => $status,
                'message' => $message,
                'data' => $result,
            ];

            return response()->json($response);
        }
    }
}
