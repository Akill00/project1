<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\JWTAuthController;


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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('register', [App\Http\Controllers\JWTAuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\JWTAuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\JWTAuthController::class, 'logout']);
    Route::post('refresh', [App\Http\Controllers\JWTAuthController::class, 'refresh']);
    Route::get('profile', [App\Http\Controllers\JWTAuthController::class, 'profile']);

    Route::apiResource('products', ProductController::class);
});