<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Các cột được phép ghi dữ liệu (mass assignable)
    protected $fillable = [
        'name', 'description', 'price', 'quantity'
    ];

    // Quan hệ với User (giả sử mỗi sản phẩm thuộc về một người dùng)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
