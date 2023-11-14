<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductConversion;
use App\Http\Resources\ProductConversionResource;
use App\Models\Product;
use Validator;

class ProductConversionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conversion = ProductConversion::latest()->get();
        $result = ProductConversionResource::collection($conversion);
        $message = 'Conversion retrieved successfully.';
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
            'sourceProduct'  => ['required'],
            'toConvertQty'  => ['required'],
            'destinationProduct'  => ['required'],
            'convertedQty'  => ['required'],
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
            $sourceProductId = $request->sourceProduct;
            $toConvertQty = $request->toConvertQty;
            $destinationProductId = $request->destinationProduct;
            $convertedQty = $request->convertedQty;

            $conversion = new ProductConversion;
            $conversion->source_product_id = $sourceProductId;
            $conversion->to_convert_qty = $toConvertQty;
            $conversion->destination_product_id = $destinationProductId;
            $conversion->converted_qty = $convertedQty;
            $conversion->save();

            $sourceProduct = Product::find($sourceProductId);
            $sourceProductQty = $sourceProduct->qty;
            $sourceProductNewQty = $sourceProductQty - $toConvertQty;
            $sourceProduct->qty = $sourceProductNewQty;
            $sourceProduct->save();
            $conversion->transactions()->create([
                'type' => 'Product Conversion',
                'product_id' => $sourceProductId,
                'opening' => $sourceProductQty,
                'out' => $toConvertQty,
                'closing' => $sourceProductNewQty
            ]);

            $destinationProduct = Product::find($destinationProductId);
            $destinationProductQty = $destinationProduct->qty;
            $destinationProductNewQty = $destinationProductQty + $convertedQty;
            $destinationProduct->qty = $destinationProductNewQty;
            $destinationProduct->save();
            $conversion->transactions()->create([
                'type' => 'Product Conversion',
                'product_id' => $destinationProductId,
                'opening' => $destinationProductQty,
                'in' => $convertedQty,
                'closing' => $destinationProductNewQty
            ]);
            $status = 200;
            $message = 'Conversion created successfully.';
            $result = new ProductConversionResource($conversion);

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
        $conversion = ProductConversion::find($id);

        if (is_null($conversion)) {
            $status = 404;
            $message = 'Conversion not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $status = 200;
            $message = 'Conversion retrieved successfully';
            $result = new ProductConversionResource($conversion);

            $response = [
                'status' => $status,
                'success' => true,
                'message' => $message,
                'data' => $result,
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
        //
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
