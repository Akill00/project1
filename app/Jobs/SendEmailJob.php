<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Khai báo các thuộc tính
    protected $email;
    protected $content;

    // Hàm khởi tạo
    public function __construct($email, $content)
    {
        $this->email = $email;
        $this->content = $content;
    }

    public function handle()
    {
        // Ghi log trước khi gửi email
        Log::info('Bắt đầu gửi email', [
            'email' => $this->email,
            'content' => $this->content,
        ]);

        try {
            // Gửi email
            Mail::raw($this->content, function ($message) {
                $message->to($this->email)
                        ->subject('Queued Email Subject')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Ghi log khi gửi email thành công
            Log::info('Gửi email thành công', [
                'email' => $this->email,
            ]);
        } catch (\Exception $e) {
            // Ghi log khi có lỗi xảy ra
            Log::error('Gửi email thất bại', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
