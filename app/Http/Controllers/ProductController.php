<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Lấy danh sách sản phẩm
    public function index()
    {
        try {
            // Lấy sản phẩm của người dùng đăng nhập
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
    
            $products = $user->products; // Kiểm tra quan hệ products đã được định nghĩa trong User model
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
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
    
        // Kiểm tra user hiện tại
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user'
            ], 401);
        }
    
        // Tạo sản phẩm mới
        $product = Product::create(array_merge($validatedData, ['user_id' => $userId]));
    
        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    // Lấy chi tiết một sản phẩm
    public function show($id)
    {
        $product = Product::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($product->user_id !== Auth::id()) {
            return response()->json([
                'error' => 'Unauthorized access'
            ], 403);
        }

        return response()->json($product, 200);
    }

    // Cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'quantity' => 'sometimes|required|integer',
        ]);

        $product->update($validatedData);
        return response()->json($product, 200);
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
