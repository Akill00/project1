<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\JWTAuthController;

use App\Jobs\CountProductsJob;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ProductCommentController;

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
Route::group(['middleware' => 'api'], function () {

    // Đăng ký người dùng
    Route::post('register', [JWTAuthController::class, 'register'])->name('register');
    // Đăng nhập người dùng
    Route::post('login', [JWTAuthController::class, 'login'])->name('login');
    // Đăng xuất người dùng
    Route::post('logout', [JWTAuthController::class, 'logout'])->name('logout');
    // Làm mới token
    Route::post('refresh', [JWTAuthController::class, 'refresh'])->name('refresh');
    // Lấy thông tin người dùng
    Route::get('profile', [JWTAuthController::class, 'profile'])->name('profile');
    // Lấy số lượng sản phẩm
    Route::get('products/count', [ProductController::class, 'countProducts'])->name('products.count');

    // Gửi email
    Route::post('send-email', [MailController::class, 'sendEmail'])->name('send.email');

});


// Routes yêu cầu xác thực

Route::group(['middleware' => 'auth:api'], function () {
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

    // Routes cho bình luận
    Route::prefix('products/{product}')->group(function () {
        // Lấy danh sách bình luận
        Route::get('comments', [ProductCommentController::class, 'index'])->name('comments.index');
        // Thêm bình luận
        Route::post('comments', [ProductCommentController::class, 'store'])->name('comments.store');
        // Cập nhật bình luận
        Route::put('comments/{comment}', [ProductCommentController::class, 'update'])->name('comments.update');
        // Xóa bình luận
        Route::delete('comments/{comment}', [ProductCommentController::class, 'destroy'])->name('comments.destroy');

    });

});