<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
    /**
     * Định dạng response theo chuẩn RESTful.
     *
     * @param bool $status  Trạng thái thành công (true/false)
     * @param string $message  Thông báo mô tả
     * @param mixed $data  Dữ liệu trả về (nếu có)
     * @param int $code  Mã HTTP (default: 200)
     * @return \Illuminate\Http\JsonResponse
     */
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
