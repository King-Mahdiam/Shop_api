<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(5);
        return $this->SuccessResponse([
            'product' => ProductResource::collection($products->load('images')) ,
            'links' => ProductResource::collection($products)->response()->getData()->links ,
            'meta' => ProductResource::collection($products)->response()->getData()->meta ,
        ] , 200 , 'true');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'name' => ['required' , 'string'] ,
            'brand_id' => ['required' , 'integer'] ,
            'category_id' => ['required' , 'integer'] ,
            'primary_image' => ['required' , 'image'] ,
            'text' => ['required'] ,
            'price' => ['integer'] ,
            'quantity' => ['integer'] ,
            'delivery_amount' => ['integer'] ,
            'images.*' => ['nullable' , 'image'] ,
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422 , $validator->messages());
        }

        $image = $request->primary_image;
        $alphabet = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $code = implode($pass);

        $format = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);

        $path = "/images/product/";
        $filename = time().'-'.$code.'.'.$format;
        $image->move(public_path().$path, $filename);
        $file_url = $path.$filename;

        $product = Product::create([
            'name' => $request->name ,
            'brand_id' => $request->brand_id ,
            'category_id' => $request->category_id ,
            'primary_image' => $file_url ,
            'text' => $request->text ,
            'price' => $request->price ,
            'quantity' => $request->quantity ,
            'delivery_amount' => $request->delivery_amount ,
        ]);

        if ($request->has('images')) {
            foreach ($request->images as $image) {
                $alphabet = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $pass = array(); //remember to declare $pass as an array
                $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                for ($i = 0; $i < 8; $i++) {
                    $n = rand(0, $alphaLength);
                    $pass[] = $alphabet[$n];
                }
                $code = implode($pass);

                $format = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);

                $path = "/images/product/";
                $filename = time().'-'.$code.'.'.$format;
                $image->move(public_path().$path, $filename);
                $file_url = $path.$filename;

                ProductImage::create([
                    'product_id' => $product->id ,
                    'image' => $file_url
                ]);
            }
        }

        return $this->SuccessResponse(new ProductResource($product) , 200 , 'product created successful');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->SuccessResponse(new ProductResource($product->load('images')), 200 , 'true');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all() , [
            'name' => ['required' , 'string'] ,
            'brand_id' => ['required' , 'integer'] ,
            'category_id' => ['required' , 'integer'] ,
            'primary_image' => ['nullable' , 'image'] ,
            'text' => ['required'] ,
            'price' => ['integer'] ,
            'quantity' => ['integer'] ,
            'delivery_amount' => ['integer'] ,
            'images.*' => ['nullable' , 'image'] ,
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422 , $validator->messages());
        }

        if ($request->has('primary_image')) {
            File::delete(public_path($product->primary_image));
            $image = $request->primary_image;
            $alphabet = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $code = implode($pass);

            $format = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);

            $path = "/images/product/";
            $filename = time().'-'.$code.'.'.$format;
            $image->move(public_path().$path, $filename);
            $file_url = $path.$filename;
        }

        $product->update([
            'name' => $request->name ,
            'brand_id' => $request->brand_id ,
            'category_id' => $request->category_id ,
            'primary_image' => $file_url ?? $product->primary_image ,
            'text' => $request->text ,
            'price' => $request->price ,
            'quantity' => $request->quantity ,
            'delivery_amount' => $request->delivery_amount ,
        ]);

        if ($request->has('images')) {
            foreach ($product->images as $item) {
                File::delete(public_path($item->image));
                ProductImage::destroy($item->id);
            }
            foreach ($request->images as $image) {
                $alphabet = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $pass = array(); //remember to declare $pass as an array
                $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                for ($i = 0; $i < 8; $i++) {
                    $n = rand(0, $alphaLength);
                    $pass[] = $alphabet[$n];
                }
                $code = implode($pass);

                $format = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);

                $path = "/images/product/";
                $filename = time().'-'.$code.'.'.$format;
                $image->move(public_path().$path, $filename);
                $file_url = $path.$filename;

                ProductImage::create([
                    'product_id' => $product->id ,
                    'image' => $file_url
                ]);
            }
        }

        return $this->SuccessResponse(new ProductResource($product) , 200 , 'product created successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        File::delete(public_path($product->primary_image));
        foreach ($product->images as $item) {
            File::delete(public_path($item->image));
            ProductImage::destroy($item->id);
        }
        $product->delete();
        return $this->SuccessResponse(new ProductResource($product) , 200 , 'product deleted successful');
    }
}
