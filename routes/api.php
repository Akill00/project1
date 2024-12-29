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

Route::middleware('api')->group(function () {
    // Lấy danh sách sản phẩm
    Route::get('products', [ProductController::class, 'index'])->name('products.index');

    // Lấy chi tiết một sản phẩm
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Tạo sản phẩm mới
    Route::post('products', [ProductController::class, 'store'])->name('products.store');

    // Cập nhật sản phẩm
    Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');

    // Xóa sản phẩm
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});