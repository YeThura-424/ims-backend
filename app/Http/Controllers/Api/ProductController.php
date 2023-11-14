<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\File;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::latest()->get();
        $slaeable_product = Product::where('qty', '>', '0')->whereNotNull('sellingprice')->get();
        $result = ProductResource::collection($product);
        $slaeable_product_result = ProductResource::collection($slaeable_product);
        $message = 'Products retrieved successfully.';
        $status = 200;

        $response = [
            'status' => $status,
            'success' => true,
            'message' => $message,
            'data' => $result,
            'saleable_product' => $slaeable_product_result
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
            'name'  => ['required', 'string', 'max:255'],
            'code'  => ['required', 'string', 'min:6', 'unique:products'],
            'uom' => ['required', 'string'],
            'photo' => 'required',
            
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
            $name = $request->name;
            $code = $request->code;
            $uom = $request->uom;
            $qty = $request->qty;
            $sku = $request->sku;
            $photo = $request->photo;
            $imageName = time() . '.' . $photo->extension();
            $photo->move(public_path('images/product'), $imageName);
            $filepath = 'images/product/' . $imageName;


            $product = new Product;
            $product->name = $name;
            $product->code = $code;
            $product->uom = $uom;
            $product->sku = $sku;
            $product->qty = $qty;
            $product->photo = $filepath;
            $product->save();

            $status = 200;
            $message = 'Product created successfully.';
            $result = new ProductResource($product);

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
        $product = Product::find($id);

        if (is_null($product)) {
            $status = 404;
            $message = 'Product not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $status = 200;
            $message = 'Product retrieved successfully';
            $result = new ProductResource($product);

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
        $product = Product::find($id);
        $name = $request->name;
        $code = $request->code;
        $uom = $request->uom;
        $qty = $request->qty;
        $sku = $name . '-' . $uom;
        $newphoto = $request->photo;
        $oldphoto = $product->photo;
        if ($request->hasFile('photo')) {
            $imageName = time() . '.' . $newphoto->extension();
            $newphoto->move(public_path('images/product'), $imageName);
            $filepath = 'images/product/' . $imageName;
            if (File::exists(public_path($oldphoto))) {
                File::delete(public_path($oldphoto));
            }
        } else {
            $filepath = $oldphoto;
        }
        // Data update
        $product->name = $name;
        $product->code = $code;
        $product->uom = $uom;
        $product->sku = $sku;
        $product->qty = $qty;
        $product->photo = $filepath;
        $product->save();

        $status = 200;
        $message = 'Product update successfully';
        $result = new ProductResource($product);

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
        $product = Product::find($id);

        if (is_null($product)) {
            $status = 404;
            $message = 'Product not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $product->delete();
            $status = 200;
            $message = 'Product deleted successfully';


            $response = [
                'success' => true,
                'status' => $status,
                'message' => $message,
            ];

            return response()->json($response);
        }
    }
}
