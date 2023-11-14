<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleInvoice;
use App\Http\Resources\SaleInvoiceResource;
use Validator;
use App\Models\Product;
use App\Models\ProductTransection;

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
                // $product->qty -= $closing;
                $product->save();

                // $product_transection = new ProductTransection;
                $saleinvoice->transactions()->create([
                    'type' => 'Sale',
                    'product_id' => $id,
                    'opening' => $opening,
                    'out' => $qty,
                    'closing' => $closing
                ]);
                // $product_transection->import_sale_id = $saleinvoiceId;
                // $product_transection->type = 'Sale';
                // $product_transection->product_id = $id;
                // $product_transection->opening = $opening;
                // $product_transection->out = $qty;
                // $product_transection->closing = $closing;
                // $product_transection->save();
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $saleinvoice = SaleInvoice::find($id);
        $combinedData = [];
        foreach ($saleinvoice->products as $product_detail) {
            $combinedData[] = [
                'id' => $product_detail->pivot->product_id,
                'name' => $product_detail->sku,
                "remainingqty" => $product_detail->qty,
                'saleqty' => $product_detail->pivot->qty,
                'previous_qty' => $product_detail->pivot->qty,
                'rate' => $product_detail->pivot->rate,
                'productamount' => $product_detail->pivot->productamount,
            ];
        }
        if (is_null($saleinvoice)) {
            $status = 404;
            $message = 'Sale not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $status = 200;
            $message = 'Sale retrieved successfully';
            $result = new SaleInvoiceResource($saleinvoice);
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
        $saleinvoice = SaleInvoice::find($id);
        $orderno = $request->orderno;
        $saledate = $request->saledate;
        $totalamount = $request->totalamount;
        $remark = $request->remark;
        $saleItem = json_decode($request->saleItem);
        $productsToRemove = json_decode($request->productsToRemove);

        // Data update

        $saleinvoice->saledate = $saledate;
        $saleinvoice->orderno = $orderno;
        $saleinvoice->remark = $remark;
        $saleinvoice->totalamount = $totalamount;
        $saleinvoice->save();

        // Update or add products
        if ($saleItem !== null) {
            $productData = [];
            foreach ($saleItem as $item) {
                $productId = $item->id;
                $qty = $item->qty;
                $previous_qty = $item->previous_qty;
                $updated_qty = $qty - $previous_qty;
                $rate = $item->rate;
                $amount = $item->productamount;

                $productData[$productId] = [
                    'qty' => $qty,
                    'rate' => $rate,
                    'productamount' => $amount,
                ];

                // $product = Product::find($productId);
                // $previous_product_qty = $product->qty;
                // $opening = $previous_product_qty - $previous_qty;
                // $closing = $opening + $qty;
                // $product->qty = $closing;
                // $product->save();


                $transaction = $saleinvoice->transactions()->where('product_id', $productId)->first();
                if ($transaction) {
                    // Update product qty
                    $product = Product::find($productId);
                    $previous_product_qty = $product->qty;
                    $opening = $previous_product_qty + $previous_qty;
                    $closing = $opening - $qty;
                    $product->qty = $closing;
                    $product->save();
                    // If the transaction exists, update its values
                    $transaction->update([
                        'opening' => $opening,
                        'out' => $qty,
                        'closing' => $closing
                    ]);
                } else {

                    $product = Product::find($id);
                    $opening = $product->qty;
                    $closing = $opening - $qty;
                    $product->qty = $closing;
                    $product->save();
                    // If the transaction doesn't exist, create a new one
                    $transaction = new ProductTransection([
                        'type' => 'Sale',
                        'product_id' => $productId,
                        'opening' => $opening,
                        'out' => $qty,
                        'closing' => $closing
                    ]);
                    $saleinvoice->transactions()->save($transaction);
                }
            }
            $saleinvoice->products()->sync($productData);


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
                    $updateQty = $productQty + $removedProductQty;
                    // Adjust the product quantity as needed based on your business logic
                    $product->qty = $updateQty;
                    $product->save();

                    $transaction = $saleinvoice->transactions()->where('product_id', $productToRemoveId)->first();

                    if ($transaction) {
                        $transaction->delete();
                    }
                }
            }
        }
        $status = 200;
        $message = 'Sale updated successfully';
        $result = new SaleInvoiceResource($saleinvoice);

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
        //
    }
}
