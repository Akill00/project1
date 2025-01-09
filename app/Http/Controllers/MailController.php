<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class MailController extends ApiController
{
    public function sendEmail(Request $request)
    {
        // Ghi lại thông tin yêu cầu
        Log::info('Received email request', [
            'email' => $request->email,
            'content' => $request->content,
        ]);

        // Validate request
        $request->validate([
            'email' => 'required|email',
            'content' => 'required|string',
        ]);

        //lấy thông tin từ yêu cầu mà người dùng gửi đến API
        $toEmail = $request->email;
        $content = $request->content;

        // Dispatch job để gửi email
        Log::info('Đang dispatch job gửi email', [
            'to_email' => $toEmail,
            'content' => $content,
        ]);
        
        // try - catch để bắt lỗi khi gửi email
        try {
            // Gửi email
            Mail::raw($content, function ($message) use ($toEmail) {
                $message->to($toEmail)
                        ->subject('email testing')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Ghi lại thông tin thành công
            Log::info('Email sent successfully', [
                'to' => $toEmail,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully to: ' . $toEmail,
            ], 200);

        } catch (\Exception $e) {
            // Ghi lại lỗi nếu có
            Log::error('Failed to send email', [
                'to' => $toEmail,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }
    }
}