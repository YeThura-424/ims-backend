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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $importtowarehouse = ImportToWarehouse::find($id);
        $importProduct = [];
        foreach ($importtowarehouse->products as $product_detail) {
            $importProduct[] = [
                'id' => $product_detail->pivot->product_id,
                'name' => $product_detail->sku,
                'receiveqty' => $product_detail->pivot->qty,
                'previous_qty' => $product_detail->pivot->qty,
                'rate' => $product_detail->pivot->rate,
                'productamount' => $product_detail->pivot->productamount,
            ];
        }
        if (is_null($importtowarehouse)) {
            $status = 404;
            $message = 'Import not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $status = 200;
            $message = 'Import retrieved successfully';
            $result = new ImportToWarehouseResource($importtowarehouse);
            $response = [
                'status' => $status,
                'success' => true,
                'message' => $message,
                'data' => $result,
                'product' => $importProduct
            ];

            return response()->json($response);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $warehouseimport = ImportToWarehouse::find($id);
        $importcode = $request->importcode;
        $date = $request->date;
        $invoicedate = $request->invoicedate;
        $vendor = $request->vendor;
        $invoiceno = $request->invoiceno;
        $totalamount = $request->totalamount;
        $remark = $request->remark;
        $importItem = json_decode($request->importItem);
        $productsToRemove = json_decode($request->productsToRemove);
        $newphoto = $request->photo;
        $oldphoto = $warehouseimport->photo;
        if ($request->hasFile('photo')) {
            $imageName = time() . '.' . $newphoto->extension();
            $newphoto->move(public_path('images/warehouseimport'), $imageName);
            $filepath = 'images/warehouseimport/' . $imageName;
            if (File::exists(public_path($oldphoto))) {
                File::delete(public_path($oldphoto));
            }
        } else {
            $filepath = $oldphoto;
        }

        // Data update
        $warehouseimport->importcode = $importcode;
        $warehouseimport->date = $date;
        $warehouseimport->invoicedate = $invoicedate;
        $warehouseimport->vendor_id = $vendor;
        $warehouseimport->invoiceno = $invoiceno;
        $warehouseimport->photo = $filepath;
        $warehouseimport->totalamount = $totalamount;
        $warehouseimport->remark = $remark;
        $warehouseimport->save();

        // Update or add products
        if ($importItem !== null) {
            $productData = [];
            foreach ($importItem as $item) {
                $productId = $item->id;
                $qty = $item->receiveqty;
                $previous_qty = $item->previous_qty;
                $updated_qty = $qty - $previous_qty;

                $rate = $item->rate;
                $amount = $item->productamount;

                $productData[$productId] = [
                    'qty' => $qty,
                    'rate' => $rate,
                    'productamount' => $amount,
                ];
            }
            $warehouseimport->products()->sync($productData);

            // Remove products from the import if needed
            if ($productsToRemove !== null) {

                foreach ($productsToRemove as $productToRemove) {
                    $productToRemoveId = $productToRemove->id;
                    $removedProductQty = $productToRemove->qty;

                    $product = Product::find($productToRemoveId);
                    $productQty = $product->qty;
                    $updateQty = $productQty - $removedProductQty;
                    // Adjust the product quantity as needed based on your business logic
                    $product->qty = $updateQty;
                    $product->save();
                }
            }
        }
        $status = 200;
        $message = 'Import updated successfully';
        $result = new ImportToWarehouseResource($warehouseimport);

        $response = [
            'status' => $status,
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response);
    }
}
