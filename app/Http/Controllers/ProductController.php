<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


use App\Jobs\CountProductsJob;

class ProductController extends ApiController
{
// Lấy danh sách sản phẩm
public function index(Request $request)
{
    try {
        $user = Auth::user();
        if (!$user) {
            return $this->response(false, 'User not authenticated', null, 401);
        }

        // Query sản phẩm của người dùng
        $query = $user->products()->orderBy('created_at', 'desc'); // Sắp xếp từ mới nhất đến cũ nhất

        // Nếu có tham số search, áp dụng tìm kiếm theo name
        if ($request->has('search') && $request->search !== null) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Sử dụng phân trang, mỗi lần trả về 10 sản phẩm
        //$products = $query->paginate(10);
        $products = $query->paginate(2); // Hiển thị 2 sản phẩm mỗi trang


        return $this->response(true, 'Products retrieved successfully', $products);
    } catch (\Exception $e) {
        return $this->response(false, 'Something went wrong', null, 500);
    }
}


    // Tạo sản phẩm mới
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return $this->response(false, 'Unauthorized user', null, 401);
        }

        $product = Product::create(array_merge($validatedData, ['user_id' => $userId]));

        return $this->response(true, 'Product created successfully', $product, 201);
    }

    // Lấy chi tiết một sản phẩm
    public function show($id)
    {
        $product = Product::find($id);
    
        // Kiểm tra nếu sản phẩm không tồn tại
        if (!$product) {
            return $this->response(false, 'Product not found', null, 404);
        }
    
        // Kiểm tra quyền sở hữu
        if ($product->user_id !== Auth::id()) {
            return $this->response(false, 'Unauthorized access', null, 403);
        }
    
        return $this->response(true, 'Product retrieved successfully', $product);
    }
    
    // Cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->user_id !== Auth::id()) {
                return $this->response(false, 'Unauthorized access', null, 403);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric',
                'quantity' => 'sometimes|required|integer',
            ]);

            $product->update($validatedData);

            return $this->response(true, 'Product updated successfully', $product);
        } catch (\Exception $e) {
            return $this->response(false, 'Product not found', null, 404);
        }
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->user_id !== Auth::id()) {
                return $this->response(false, 'Unauthorized access', null, 403);
            }

            $product->delete();

            return $this->response(true, 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->response(false, 'Product not found', null, 404);
        }
    }


    public function countProducts()
    {
        // Dispatch job để đếm sản phẩm
        CountProductsJob::dispatch();
        Log::info('CountProducts job dispatched.');
        // Trả về phản hồi với kết quả
        $count = Redis::get('total_products');
        return response()->json([
            'status' => true,
            'count' => $count,
            'message' => 'Products counted successfully.',
        ], 200);
    } 


   /* public function countProducts()
    {
        // Trực tiếp đếm số lượng sản phẩm mà không cần sử dụng job
        $count = Product::count();

        // Trả về số lượng sản phẩm
        return response()->json(['count' => $count]);
    }
    */



}
