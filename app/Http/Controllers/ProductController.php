<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;

class ProductController extends ApiController
{
    // Khai báo biến để lưu trữ ProductService
    protected $productService;

    // Khởi tạo ProductController với ProductService
    public function __construct(ProductService $productService)
    {
        // Gán ProductService vào biến productService
        $this->productService = $productService;
    }

    // Hàm lấy tất cả sản phẩm
    public function index(Request $request)
    {
        try {
            // Gọi hàm lấy tất cả sản phẩm từ ProductService
            $result = $this->productService->getAllProducts($request);
            // Nếu không lấy được sản phẩm thì trả về thông báo lỗi
            if (!$result['success']) {
                // Ghi log lỗi
                Log::error('Failed to retrieve products', ['status' => $result['status'], 'message' => $result['message']]);
                // Trả về thông báo lỗi
                return $this->response(false, $result['message'], null, $result['status'] ?? 500);
            }
            // Trả về thông báo thành công và danh sách sản phẩm
            return $this->response(true, 'Products retrieved successfully', $result['data'], 200);
        } catch (\Exception $e) {
            Log::error('Unexpected error in retrieving products', ['error' => $e->getMessage()]);
            return $this->response(false, 'Unexpected error', null, 500);
        }
    }

    // Hàm lấy sản phẩm theo id
    public function show($id)
    {
        try {
            // Gọi hàm lấy sản phẩm theo id từ ProductService
            $product = $this->productService->getProductById($id);
            if (!$product) {
                Log::warning('Product not found', ['product_id' => $id]);
                return $this->response(false, 'Product not found', null, 404);
            }
            // Trả về thông báo thành công và sản phẩm
            return $this->response(true, 'Product retrieved successfully', $product, 200);
        } catch (\Exception $e) {
            Log::error('Unexpected error in retrieving product', ['product_id' => $id, 'error' => $e->getMessage()]);
            return $this->response(false, 'Unexpected error', null, 500);
        }
    }

    // Hàm tạo sản phẩm
    public function store(Request $request)
    {
        try {
            // Validate dữ liệu request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
            ]);
            // Gọi hàm tạo sản phẩm từ ProductService
            $result = $this->productService->createProduct($validatedData);
            if (!$result['success']) {
                Log::error('Failed to create product', ['message' => $result['message']]);
                return $this->response(false, $result['message'], null, $result['status']);
            }
            // Trả về thông báo thành công và sản phẩm đã tạo
            return $this->response(true, 'Product created successfully', $result['data'], 201);
        } catch (\Exception $e) {
            Log::error('Unexpected error in creating product', ['error' => $e->getMessage()]);
            return $this->response(false, 'Unexpected error', null, 500);
        }
    }

    // Hàm cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric',
                'quantity' => 'sometimes|required|integer',
            ]);
            // Gọi hàm cập nhật sản phẩm từ ProductService
            $result = $this->productService->updateProduct($validatedData, $id);
            if (!$result['success']) {
                Log::error('Failed to update product', ['product_id' => $id, 'message' => $result['message']]);
                return $this->response(false, $result['message'], null, $result['status']);
            }
            return $this->response(true, 'Product updated successfully', $result['data']);
        } catch (\Exception $e) {
            Log::error('Unexpected error in updating product', ['product_id' => $id, 'error' => $e->getMessage()]);
            return $this->response(false, 'Unexpected error', null, 500);
        }
    }

    // Hàm xóa sản phẩm
    public function destroy($id)
    {
        try {
            // Gọi hàm xóa sản phẩm từ ProductService
            $result = $this->productService->deleteProduct($id);
            if (!$result['success']) {
                Log::error('Failed to delete product', ['product_id' => $id, 'message' => $result['message']]);
                return $this->response(false, $result['message'], null, $result['status']);
            }
            return $this->response(true, 'Product deleted successfully');
        } catch (\Exception $e) {
            Log::error('Unexpected error in deleting product', ['product_id' => $id, 'error' => $e->getMessage()]);
            return $this->response(false, 'Unexpected error', null, 500);
        }
    }

    // Hàm đếm số lượng sản phẩm
    public function countProducts()
    {
        try {
            // Gọi hàm đếm số lượng sản phẩm từ ProductService
            $count = $this->productService->countProducts();
            Log::info('Products counted', ['count' => $count]);
            return $this->response(true, 'Products counted successfully.', ['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Unexpected error in counting products', ['error' => $e->getMessage()]);
            return $this->response(false, 'Unexpected error', null, 500);
        }
    }
}
