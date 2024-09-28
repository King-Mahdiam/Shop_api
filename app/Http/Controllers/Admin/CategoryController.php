<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(5);
        return $this->SuccessResponse([
            'brands' => CategoryResource::collection($categories) ,
            'links' => CategoryResource::collection($categories)->response()->getData()->links ,
            'meta' => CategoryResource::collection($categories)->response()->getData()->meta ,
        ] , 200 , 'true');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'parent_id' => ['required' , 'integer'] ,
            'name' => ['required' , 'string' , 'unique:categories,name'] ,
            'text' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422 , $validator->messages());
        }

        $category = Category::create([
            'parent_id' => $request->parent_id ,
            'name' => $request->name ,
            'text' => $request->text
        ]);

        return $this->SuccessResponse(new CategoryResource($category) , 200 , 'category created successful');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->SuccessResponse($category , 200 , 'true');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all() , [
            'parent_id' => ['required' , 'integer'] ,
            'name' => ['required' , 'string' , 'unique:categories,name,' . $category->id] ,
            'text' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422 , $validator->messages());
        }

        $category->update([
            'parent_id' => $request->parent_id ,
            'name' => $request->name ,
            'text' => $request->text
        ]);

        return $this->SuccessResponse(new CategoryResource($category) , 200 , 'category updated successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $data = $category->delete();
        return $this->SuccessResponse($data , 200 , 'category deleted successful');
    }

    public function child(Category $category)
    {
        return $this->SuccessResponse(new CategoryResource($category->load('child')) , 201);
    }

}
