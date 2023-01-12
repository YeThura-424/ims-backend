<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::latest()->get();
        $result = CategoryResource::collection($category);
        $message = 'Category retrieved successfully.';
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
            'name'  => ['required', 'string', 'max:255', 'unique:categories'],
            'code'  => ['required', 'string', 'max:255', 'unique:categories'],
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


            $category = new Category;
            $category->name = $name;
            $category->code = $code;
            $category->save();

            $status = 200;
            $message = 'Category created successfully.';
            $result = new CategoryResource($category);

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
