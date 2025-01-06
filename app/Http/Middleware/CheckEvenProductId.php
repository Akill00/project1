<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;

class CheckEvenProductId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
public function handle(Request $request, Closure $next)
{
    // Lấy ID sản phẩm từ tham số route
    $productId = $request->route('id');

        // Nếu không có ID hoặc ID không phải số nguyên hợp lệ, bỏ qua kiểm tra
        if (!is_numeric($productId)) {
            return $next($request);
        }

        // Lấy sản phẩm từ database
        $product = \App\Models\Product::find($productId);

        // Nếu sản phẩm không tồn tại, bỏ qua và để controller xử lý
        if (!$product) {
            return $next($request);
        }


        // Kiểm tra nếu có ID và ID đó là số lẻ
        if ($productId && $productId % 2 !== 0) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. Product ID must be even.',
            ], 403);
        }

        return $next($request);
    }

}
