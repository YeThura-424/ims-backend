<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Http\Resources\VendorResource;
use Validator;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendor = Vendor::latest()->get();
        $result = VendorResource::collection($vendor);
        $message = 'Vendor retrieved successfully.';
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
            'name'  => ['required', 'string', 'max:255', 'unique:vendors'],
            'code'  => ['required', 'string', 'max:255', 'unique:vendors'],
            'type'  => ['required'],
            'paymenttype' => ['required'],
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
            $type = $request->type;
            $paymenttype = $request->paymenttype;
            $isactive = $request->status;
            if ($isactive == "true") {
                $is_active = 1;
            } else {
                $is_active = 0;
            }


            $vendor = new Vendor;
            $vendor->name = $name;
            $vendor->code = $code;
            $vendor->type = $type;
            $vendor->paymenttype = $paymenttype;
            $vendor->is_active = $is_active;
            $vendor->save();

            $status = 200;
            $message = 'Vendor created successfully.';
            $result = new VendorResource($vendor);

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
        $vendor = Vendor::find($id);

        if (is_null($vendor)) {
            $status = 404;
            $message = 'Vendor not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $status = 200;
            $message = 'Vendor retrieved successfully';
            $result = new VendorResource($vendor);

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
        $vendor = Vendor::find($id);

        $name = $request->name;
        $code = $request->code;
        $type = $request->type;
        $paymenttype = $request->paymenttype;
        $isactive = $request->status;
        if ($isactive == "true") {
            $is_active = 1;
        } else {
            $is_active = 0;
        }



        //Data Update

        $vendor->name = $name;
        $vendor->code = $code;
        $vendor->type = $type;
        $vendor->paymenttype = $paymenttype;
        $vendor->is_active = $is_active;
        $vendor->save();

        $status = 200;
        $message = "Vendor Updated Successfully";
        $result = new VendorResource($vendor);

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
        $vendor = Vendor::find($id);

        if (is_null($vendor)) {
            $status = 404;
            $message = 'Vendor not found';

            $response = [
                'status' => $status,
                'success' => false,
                'message' => $message,
            ];

            return response()->json($response);
        } else {
            $vendor->delete();
            $status = 200;
            $message = 'Vendor deleted successfully';


            $response = [
                'success' => true,
                'status' => $status,
                'message' => $message,
            ];

            return response()->json($response);
        }
    }
}
