<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CountProductsAndSendEmail extends Command
{
    protected $signature = 'products:count-send-email';
    protected $description = 'Count the total number of products and send an email at midnight';

    public function handle()
    {
        // Đếm số lượng sản phẩm
        $count = Product::count();
        // Lấy danh sách sản phẩm
        $products = Product::select('id', 'name')->get();
        // Địa chỉ email nhận thông báo
        $email = 'dangtrien0@gmail.com';
    
        // Tạo nội dung email
        $content = "There are {$count} products in the database as of " . now()->toDateTimeString() . "\n\n";
        // Thêm danh sách sản phẩm vào nội dung
        $content .= "Product List:\n";
    
        // Lặp qua danh sách sản phẩm và thêm vào nội dung
        foreach ($products as $product) {
            // Thêm thông tin sản phẩm vào nội dung email
            $content .= "ID: {$product->id}, Name: {$product->name}\n"; // Đảm bảo thêm xuống dòng tránh lỗi không hiển thị
        }
    
        // Ghi log nội dung email trước khi gửi
        Log::info('Product count email content:', ['content' => $content]);
    
        try {
            // Gửi email
            Mail::raw($content, function ($message) use ($email) {
                $message->to($email)
                        ->subject('Daily Product Count')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
    
            // Hiển thị thông báo khi gửi email thành công
            $this->info('Email sent successfully with product count and details.');
            Log::info('Email sent successfully.'); // Ghi log nếu gửi email thành công
        } catch (\Exception $e) {
            // Ghi log nếu có lỗi xảy ra
            Log::error('Failed to send product count email.', ['error' => $e->getMessage()]);
        }
    }
}
