<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ImportToWarehouseResource;
use App\Models\ImportToWarehouse;
use Validator;
use App\Models\Product;

class ImportToWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $import = ImportToWarehouse::all();
        $result = ImportToWarehouseResource::collection($import);
        $message = 'Imports retrieved successfully.';
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
            'invoicedate'  => ['required', 'string', 'max:255'],
            'vendor'  => ['required'],
            'invoiceno' => 'required',
            'photo' => 'required|mimes:jpeg,bmp,png,jpg',
            'totalamount'  => ['required'],
            'importItem'  => ['required'],
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
            $date = $request->date;
            $invoicedate = $request->invoicedate;
            $vendor = $request->vendor;
            $invoiceno = $request->invoiceno;
            $totalamount = $request->totalamount;
            $remark = $request->remark;
            $photo = $request->photo;
            $imageName = time() . '.' . $photo->extension();
            $photo->move(public_path('images/warehouseimport'), $imageName);
            $filepath = 'images/warehouseimport/' . $imageName;
            $importItem = json_decode($request->importItem);
            $uniqid = uniqid();
            $importcode = "IMP-" . $date . '-' . $uniqid;
            $importtowarehouse = new ImportToWarehouse;
            $importtowarehouse->importcode = $importcode;
            $importtowarehouse->date = $date;
            $importtowarehouse->invoicedate = $invoicedate;
            $importtowarehouse->vendor_id = $vendor;
            $importtowarehouse->invoiceno = $invoiceno;
            $importtowarehouse->photo = $filepath;
            $importtowarehouse->totalamount = $totalamount;
            $importtowarehouse->remark = $remark;
            $importtowarehouse->save();

            foreach ($importItem as $item) {
                $id = $item->id;
                $qty = $item->receiveqty;
                $rate = $item->rate;
                $amount = $item->productamount;

                $importtowarehouse->products()->attach($id, [
                    'qty' => $qty,
                    'rate' => $rate,
                    'productamount' => $amount,
                ]);

                $product = Product::find($id);
                $opening = $product->qty;
                $closing = $opening + $qty;
                $product->qty = $closing;
                $product->buyingprice = $rate;
                $product->save();
            }
            $status = 200;
            $message = 'Import created successfully.';
            $result = new ImportToWarehouseResource($importtowarehouse);

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
