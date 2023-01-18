<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MaterialController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('logout',[UserController::class,'logout']);
    Route::patch('register/{id}',[UserController::class,'destroy']);
    Route::put('register/{id}',[UserController::class,'update']);
    Route::apiResource('register',UserController::class);
    Route::apiResource('category',CategoryController::class);
    Route::apiResource('material',MaterialController::class);
});
    Route::post('login',[UserController::class,'login']);
    Route::post('register',[UserController::class,'register']);
