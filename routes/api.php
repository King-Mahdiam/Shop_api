<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::apiResource('/brand' , BrandController::class);
Route::get('/brand/{brand}/products' , [BrandController::class , 'products']);
Route::apiResource('/category' , CategoryController::class);
Route::get('/category/{category}/child' , [CategoryController::class , 'child']);
Route::get('/category/{category}/products' , [CategoryController::class , 'products']);
Route::apiResource('/product' , ProductController::class);

Route::get('/pay' , [PayController::class , 'pay']);
Route::get('/verify' , [PayController::class , 'verify']);
