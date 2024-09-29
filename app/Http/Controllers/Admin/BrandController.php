<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends ApiResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::paginate(5);
        return $this->SuccessResponse([
            'brands' => BrandResource::collection($brands) ,
            'links' => BrandResource::collection($brands)->response()->getData()->links ,
            'meta' => BrandResource::collection($brands)->response()->getData()->meta ,
        ] , 200 , 'true');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all() , [
           'name' => ['required' , 'string'] ,
           'display_name' => ['required' , 'string' , 'unique:brands,display_name']
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422 , $validator->messages());
        }

        $brand = Brand::create([
           'name' => $request->name ,
           'display_name' => $request->display_name
        ]);

        return $this->SuccessResponse(new BrandResource($brand) , 200 , 'brand created successful');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return $this->SuccessResponse($brand , 200 , 'true');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all() , [
            'name' => ['required' , 'string'] ,
            'display_name' => ['required' , 'string' , 'unique:brands,display_name,' . $brand->id]
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422 , $validator->messages());
        }

        $brand->update([
            'name' => $request->name ,
            'display_name' => $request->display_name
        ]);

        return $this->SuccessResponse(new BrandResource($brand) , 200 , 'brand updated successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $data = $brand->delete();
        return $this->SuccessResponse($data , 200 , 'brand deleted successful');
    }

    public function products(Brand $brand)
    {
        return $this->SuccessResponse(new BrandResource($brand->load('products')) , 200 , 'brand updated successful');
    }
}
