<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $query = $user->products();

        // Nếu có tham số search, áp dụng tìm kiếm theo name
        if ($request->has('search') && $request->search !== null) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Lấy danh sách sản phẩm
        $products = $query->get();

        return $this->response(true, 'Products retrieved successfully', $products);
    } catch (\Exception $e) {
        return $this->response(false, 'Something went wrong', $e->getMessage(), 500);
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
        try {
            $product = Product::findOrFail($id);

            if ($product->user_id !== Auth::id()) {
                return $this->response(false, 'Unauthorized access', null, 403);
            }

            return $this->response(true, 'Product retrieved successfully', $product);
        } catch (\Exception $e) {
            return $this->response(false, 'Product not found', null, 404);
        }
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
}
