<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductCommentController extends ApiController
{
    // Phương thức để lấy danh sách bình luận cho một sản phẩm
    public function index(Product $product)
    {
        // Lấy tất cả bình luận cho sản phẩm
        $comments = $product->comments()->with('user')->get();
        
        Log::info('Lấy danh sách bình luận cho sản phẩm', [
            'product_id' => $product->id,
            'comments_count' => $comments->count(),
        ]);

        return $this->response(true, 'Comments retrieved successfully', $comments);
    }
    
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = new ProductComment();
        $comment->comment = $request->comment;
        $comment->user_id = auth()->user()->id; // Lưu ID người dùng hiện tại

        try {
            $product->comments()->save($comment);
            Log::info('Thêm bình luận thành công', [
                'product_id' => $product->id,
                'comment' => $comment->comment,
                'user_id' => $comment->user_id,
            ]);

            return $this->response(true, 'Comment added', $comment, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm bình luận', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return $this->response(false, 'Failed to add comment', null, 500);
        }
    }

    public function update(Request $request, Product $product, ProductComment $comment)
    {
        if (auth()->user()->id !== $comment->user_id) {
            return $this->response(false, 'Action Forbidden', null, 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        try {
            $comment->comment = $request->comment;
            $comment->save();

            Log::info('Cập nhật bình luận thành công', [
                'product_id' => $product->id,
                'comment_id' => $comment->id,
                'new_comment' => $comment->comment,
            ]);

            return $this->response(true, 'Comment updated', $comment);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật bình luận', [
                'product_id' => $product->id,
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
            ]);

            return $this->response(false, 'Failed to update comment', null, 500);
        }
    }

    public function destroy(Product $product, ProductComment $comment)
    {
        if (auth()->user()->id !== $comment->user_id) {
            return $this->response(false, 'Action Forbidden', null, 403);
        }

        try {
            $comment->delete();
            Log::info('Xóa bình luận thành công', [
                'product_id' => $product->id,
                'comment_id' => $comment->id,
            ]);

            return $this->response(true, 'Comment deleted successfully', null, 204);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa bình luận', [
                'product_id' => $product->id,
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
            ]);

            return $this->response(false, 'Failed to delete comment', null, 500);
        }
    }

    // Phương thức chuẩn hóa phản hồi
    public function response($status, $message, $data = null, $code = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}