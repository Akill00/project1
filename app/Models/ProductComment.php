<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    // Định nghĩa bảng tương ứng
    protected $table = 'comments';

    // Các thuộc tính có thể gán
    protected $fillable = [
        'product_id',
        'user_id',
        'comment',
    ];

    // Mối quan hệ với sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Mối quan hệ với người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}