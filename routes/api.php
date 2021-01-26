<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {

    Route::post('/signup', [\App\Http\Controllers\Api\AuthController::class, 'signUp']);
    Route::post('/signin', [\App\Http\Controllers\Api\AuthController::class, 'signIn']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    });

});
