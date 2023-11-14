<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImportDetailResource;
use Illuminate\Http\Request;
use App\Http\Resources\ImportToWarehouseResource;
use App\Models\ImportToWarehouse;
use Illuminate\Support\Facades\File;
use Validator;
use App\Models\Product;
use App\Models\ProductTransection;
use Illuminate\Support\Facades\DB;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

                $importtowarehouse->transactions()->create([
                    'type' => 'Purchase',
                    'product_id' => $id,
                    'opening' => $opening,
                    'in' => $qty,
                    'closing' => $closing
                ]);
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
        $combinedData = [];
        foreach ($importtowarehouse->products as $product_detail) {
            $combinedData[] = [
                'id' => $product_detail->pivot->product_id,
                'name' => $product_detail->sku,
                // 'uom' => $product_detail->pivot->uom,
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
                'product' => $combinedData
            ];

            return response()->json($response);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
                // $uom = $item->uom;
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

                // Update or create the transaction for this product
                $transaction = $warehouseimport->transactions()->where('product_id', $productId)->first();
                if ($transaction) {
                    // Update product qty
                    if ($updated_qty !== 0) {
                        $product = Product::find($productId);
                        $opening = $product->qty;
                        $closing = $opening + $updated_qty;
                        $product->qty = $closing;
                        $product->save();


                        $transaction = new ProductTransection([
                            'type' => 'Purchase',
                            'product_id' => $productId,
                            'opening' => $opening,
                            'in' => $updated_qty,
                            'closing' => $closing
                        ]);
                        $warehouseimport->transactions()->save($transaction);
                    }
                } else {

                    $product = Product::find($productId);
                    $opening = $product->qty;
                    $closing = $opening + $qty;
                    $product->qty = $closing;
                    $product->save();
                    // If the transaction doesn't exist, create a new one
                    $transaction = new ProductTransection([
                        'type' => 'Purchase',
                        'product_id' => $productId,
                        'opening' => $opening,
                        'in' => $qty,
                        'closing' => $closing
                    ]);
                    $warehouseimport->transactions()->save($transaction);
                }

                $product = Product::find($productId);
                $product->buyingprice = $rate;
                $product->save();
            }
            $warehouseimport->products()->sync($productData);

            // Remove products from the import if needed
            if ($productsToRemove !== null) {



                // Detach the products from the import
                // $warehouseimport->products()->detach($productsToRemove);

                // Optionally, you can update the quantity in the product table for the removed products.
                // This depends on your specific business logic.
                // For example, you might want to reduce the quantity in stock when removing products from an import.
                // You can loop through the products and update their quantities accordingly.

                foreach ($productsToRemove as $productToRemove) {
                    $productToRemoveId = $productToRemove->id;
                    $removedProductQty = $productToRemove->qty;

                    $product = Product::find($productToRemoveId);
                    $productQty = $product->qty;
                    $updateQty = $productQty - $removedProductQty;
                    // Adjust the product quantity as needed based on your business logic
                    $product->qty = $updateQty;
                    $product->save();

                    $transaction = $warehouseimport->transactions()->where('product_id', $productToRemoveId)->first();

                    if ($transaction) {
                        $transaction->delete();
                    }
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $warehouseimport = ImportToWarehouse::find($id);

        if (is_null($warehouseimport)) {
            $status = 404;
            $message = 'Import not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {

            // Soft delete the importtowarehouse record
            $warehouseimport->delete();

            $status = 200;
            $message = 'Import deleted successfully';


            $response = [
                'success' => true,
                'status' => $status,
                'message' => $message,
            ];

            return response()->json($response);
        }
    }
}
