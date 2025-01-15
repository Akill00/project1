<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductService
{
    // Khai báo biến để lưu trữ ProductRepository
    protected $productRepository; 
    // Khởi tạo ProductService với ProductRepository
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository; // Gán ProductRepository vào biến productRepository
    }
    
    // Hàm lấy tất cả sản phẩm
    public function getAllProducts($request)
    {
        // Thử Try - catch lỗi
        try {
            $user = Auth::user();
            if (!$user) {
                // Nếu không có user thì trả về thông báo lỗi
                return ['success' => false, 'message' => 'User not authenticated', 'status' => 401];
            }
            // Gọi hàm lấy tất cả sản phẩm của user
            $products = $this->productRepository->getUserProducts($user, $request->search ?? null);
            // Nếu không có sản phẩm nào thì trả về thông báo lỗi
            if ($products->isEmpty()) {
                return ['success' => false, 'message' => 'No products found', 'status' => 404];
            }
    
            return ['success' => true, 'data' => $products];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 500];
        }
    }

    // Hàm tạo sản phẩm
    public function createProduct($data)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                // Nếu không có user thì trả về thông báo lỗi
                return ['success' => false, 'message' => 'Unauthorized user', 'status' => 401];
            }
            $product = $this->productRepository->create($data, $userId); // Tạo sản phẩm
            // Nếu không tạo được sản phẩm thì trả về thông báo lỗi
            return ['success' => true, 'data' => $product, 'message' => 'Product created successfully', 'status' => 201];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 500];
        }
    }

    // Hàm lấy sản phẩm theo id
    public function getProductById($id)
    {
        try {
            $product = $this->productRepository->findWithComments($id); // Lấy sản phẩm theo id
            if (!$product) {
                // Nếu không tìm thấy sản phẩm thì trả về thông báo lỗi
                Log::warning('Product not found', ['product_id' => $id]); // Ghi log
                return ['success' => false, 'message' => 'Product not found', 'status' => 404];
            }
            // Trả về thông tin sản phẩm
            Log::info('Product retrieved successfully', ['product' => $product->toArray()]);
            return ['success' => true, 'data' => $product, 'message' => 'Product retrieved successfully', 'status' => 200];
        } catch (\Exception $e) {
            Log::error('Exception in getProductById', ['product_id' => $id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 500];
        }
    }
    
    // Hàm cập nhật sản phẩm
    public function updateProduct($data, $id)
    {
        try {
            $result = $this->productRepository->update($data, $id); // Cập nhật sản phẩm
            Log::info('Update product result', ['result' => $result]); 
            if (!$result['success']) {
                Log::warning('Failed to update product', ['product_id' => $id, 'message' => $result['message']]);
                return ['success' => false, 'message' => $result['message'] ?? 'Failed to update product', 'status' => $result['status'] ?? 404];
            }
            return ['success' => true, 'data' => $result['data'], 'message' => 'Product updated successfully', 'status' => 200];
        } catch (\Exception $e) {
            Log::error('Exception in updateProduct', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 500];
        }
    }
    
    // Hàm xóa sản phẩm
    public function deleteProduct($id)
    {
        try {
            $result = $this->productRepository->delete($id); // Xóa sản phẩm
            // Nếu không xóa được sản phẩm thì trả về thông báo lỗi
            if (!$result['success']) {
                Log::warning('Failed to delete product', ['product_id' => $id, 'message' => $result['message']]);
                return $result;
            }
            // Trả về thông báo xóa sản phẩm thành công
            Log::info('Product deleted successfully', ['product_id' => $id]);
            return ['success' => true, 'message' => 'Product deleted successfully', 'status' => 200];
            // Trả về thông báo lỗi
        } catch (\Exception $e) {
            Log::error('Exception in deleteProduct', ['product_id' => $id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 500];
        }
    }
    
    // Hàm đếm số lượng sản phẩm
    public function countProducts()
    {
        try {
            $count = $this->productRepository->count(); // Đếm số lượng sản phẩm
            Log::info('Products counted', ['count' => $count]);
            return ['success' => true, 'data' => ['count' => $count], 'message' => 'Products counted successfully', 'status' => 200];
        } catch (\Exception $e) { // Bắt lỗi
            Log::error('Exception in countProducts', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 500];
        }
    }
    
}
