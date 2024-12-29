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
        //$products = Product::all();
       // return response()->json($products);

        $products = Auth::user()->products; // Lấy sản phẩm của người dùng đăng nhập
        return response()->json($products);
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

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    // Lấy chi tiết một sản phẩm
    public function show($id)
    {
        $product = Product::findOrFail($id);
    
        // Kiểm tra quyền sở hữu
        if ($product->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
    
        return response()->json($product);
    }
    

    // Cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'quantity' => 'sometimes|required|integer',
        ]);

        $product->update($validatedData);
        return response()->json($product);
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
