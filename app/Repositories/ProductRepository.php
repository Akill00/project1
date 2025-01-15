<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductRepository
{
    // Hàm lấy tất cả sản phẩm của user
    public function getUserProducts($user, $search = null)
    {
        // Lấy tất cả sản phẩm của user theo thời gian tạo mới nhất
        $query = $user->products()->with(['comments.user'])->orderBy('created_at', 'desc');
        // Nếu có từ khóa tìm kiếm thì thêm điều kiện tìm kiếm
        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%'); // Tìm kiếm theo tên sản phẩm
        }
        
        // Phân trang với 2 sản phẩm trên mỗi trang
        $products = $query->paginate(2);
        Log::info('Products retrieved', ['products' => $products->toArray()]);
    
        return $products;
    }

    // Hàm tạo sản phẩm
    public function create(array $data, $userId)
    {
        // Tạo sản phẩm mới với thông tin và id của user
        return Product::create(array_merge($data, ['user_id' => $userId]));
    }

    // Hàm lấy sản phẩm theo id
    public function findWithComments($id)
    {
        // Lấy sản phẩm theo id với thông tin comment và user của comment
        return Product::with(['comments.user'])->find($id);
    }

    // Hàm cập nhật sản phẩm
    public function update(array $data, $id)
    {
        // Tìm sản phẩm theo id
        $product = Product::find($id);
        // Nếu không tìm thấy sản phẩm thì trả về thông báo lỗi
        if (!$product) {
            Log::warning('Product not found', ['product_id' => $id]);
            return ['success' => false, 'message' => 'Product not found', 'status' => 404];
        }
        $product->update($data); // Cập nhật thông tin sản phẩm
        Log::info('Product updated', ['product' => $product->toArray()]);
        return ['success' => true, 'data' => $product, 'status' => 200];
    }

    // Hàm xóa sản phẩm
    public function delete($id)
    {
        // Tìm sản phẩm theo id
        $product = Product::findOrFail($id);
        //  Kiểm tra xem user hiện tại có quyền xóa sản phẩm không
        if ($product->user_id !== Auth::id()) {
            // Nếu không có quyền thì trả về thông báo lỗi
            return ['success' => false, 'message' => 'Unauthorized access', 'status' => 403];
        }
        $product->delete(); // Xóa sản phẩm
        return true;
    }

    // Hàm đếm số lượng sản phẩm
    public function count()
    {
        // Đếm số lượng sản phẩm
        return Product::count();
    }
}
