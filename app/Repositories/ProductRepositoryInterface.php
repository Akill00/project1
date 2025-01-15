<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function getAll(); // Lấy tất cả sản phẩm
    public function findById($id); // Lấy sản phẩm theo id
    public function create(array $data); // Tạo sản phẩm
    public function update($id, array $data); // Cập nhật sản phẩm
    public function delete($id); // Xóa sản phẩm
}
