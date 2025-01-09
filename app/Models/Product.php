<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Các cột được phép ghi dữ liệu (mass assignable)
    protected $fillable = [
        'name', 'description', 'price', 'quantity', 'user_id' // Thêm 'user_id'
    ];

    /**
     * Quan hệ với User.
     * Mỗi sản phẩm thuộc về một người dùng.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }    

    /**
     * Quan hệ với Comment.
     * Mỗi sản phẩm có nhiều comment.
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\ProductComment::class);
    }
}
