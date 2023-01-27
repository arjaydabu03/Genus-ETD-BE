<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\TagAccountController;
use App\Http\Controllers\Api\OrderController;
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
        Route::patch('user/{id}',[UserController::class,'destroy']);
        Route::put('user/reset',[UserController::class,'reset_password']);
        Route::put('user/old_password/{id}',[UserController::class,'old_password']);
        Route::put('user/change_password/{id}',[UserController::class,'change_password']);
        Route::put('user/{id}',[UserController::class,'update']);
        
     
        Route::apiResource('user',UserController::class);

        Route::apiResource('category',CategoryController::class);

        Route::apiResource('material',MaterialController::class);
        
        Route::apiResource('tagaccount',UserController::class);

        Route::apiResource('order',OrderController::class);
    });
    Route::post('login',[UserController::class,'login']);
    Route::post('store',[UserController::class,'store']);
