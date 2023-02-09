<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\TagAccountController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TypeController;
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

// Route::get('genus_location', [UserController::class, 'get_genus_location']);

    Route::group(['middleware' => ['auth:sanctum']], function(){
        Route::post('logout',[UserController::class,'logout']);
        Route::patch('user/{id}',[UserController::class,'destroy']);
        Route::post('code_validate',[UserController::class,'code_validate']);
        Route::post('validate_username',[UserController::class,'validate_username']);
        Route::post('validate_mobile',[UserController::class,'validate_mobile']);
        Route::post('validate_name',[UserController::class,'validate_name']);
        Route::put('user/reset/{id}',[UserController::class,'reset_password']);
        Route::put('user/old_password/{id}',[UserController::class,'old_password']);
        Route::put('user/change_password/',[UserController::class,'change_password']);
        Route::put('user/{id}',[UserController::class,'update']);
        Route::apiResource('user',UserController::class);

        Route::patch('category/{id}',[CategoryController::class,'destroy']);
        Route::apiResource('category',CategoryController::class);
      
        Route::apiResource('material',MaterialController::class);
        Route::post('validate_code',[MaterialController::class,'validate_code']);

        Route::apiResource('tagaccount',TagAccountController::class);
        
        Route::apiResource('order',OrderController::class);
        Route::patch('order/{id}',[OrderController::class,'destroy']);
    });
        Route::post('login',[UserController::class,'login']);