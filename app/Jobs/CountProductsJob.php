<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductContronller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CountProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('CountProducts job is being processed.');
    
        // Đếm số lượng sản phẩm
        $count = Product::count();
        Log::info("Tổng số sản phẩm: {$count}");
        // Lưu kết quả vào Redis
        Redis::set('total_products', $count);
    }
}
