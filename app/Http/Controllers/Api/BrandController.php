<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Resources\BrandResource;
use Dotenv\Validator as DotenvValidator;
use Illuminate\Auth\Events\Validated;
use Illuminate\Validation\Validator as ValidationValidator;
use Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brand = Brand::latest()->get();
        $result = BrandResource::collection($brand);
        $message = "Brands retrieved successfully.";
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
            'name'  => ['required', 'string', 'max:255', 'unique:brands'],
            'code'  => ['required', 'string', 'max:255', 'unique:brands'],
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


            $brand = new Brand;
            $brand->name = $name;
            $brand->code = $code;
            $brand->save();

            $status = 200;
            $message = 'Brand created successfully.';
            $result = new BrandResource($brand);

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
        $brand = Brand::find($id);

        if (is_null($brand)) {
            $status = 404;
            $message = 'Brand not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $status = 200;
            $message = 'Brand retrieved successfully';
            $result = new BrandResource($brand);

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

        echo ($request);
        $brand = Brand::find($id);

        $name = $request->name;
        $code = $request->code;

        //Data Update

        $brand->name = $name;
        $brand->code = $code;
        $brand->save();

        $status = 200;
        $message = "Brand Updated Successfully";
        $result = new BrandResource($brand);

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
        $brand = Brand::find($id);

        if (is_null($brand)) {
            $status = 404;
            $message = 'Brand not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $brand->delete();
            $status = 200;
            $message = 'Brand deleted successfully';


            $response = [
                'success' => true,
                'status' => $status,
                'message' => $message,
            ];

            return response()->json($response);
        }
    }
}
