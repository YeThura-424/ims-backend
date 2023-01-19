<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Resources\BrandResource;
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
}
