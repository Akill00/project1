<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Support\Facades\Log;


class JWTAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function logout()
    {
        try {
            // Kiểm tra token trước khi invalidate
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 400);
            }

            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Failed to logout, token might be invalid or missing'], 500);
        }
    }


    public function refresh()
    {
        try {
            // Kiểm tra token hiện tại
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token is missing or invalid',
                ], 400);
            }

            // Làm mới token
            $newToken = JWTAuth::refresh();

            // Trả về token mới
            return response()->json([
                'status' => true,
                'message' => 'Token refreshed successfully',
                'token' => $newToken
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            // Xử lý lỗi nếu không thể làm mới token
            return response()->json([
                'status' => false,
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function profile()
    {
        //return response()->json(auth()->user());
        Log::info('Request received in profile endpoint');

    try {
        $user = JWTAuth::parseToken()->authenticate();
        Log::info('Authenticated user:', ['user' => $user]);

        return response()->json($user);
    } catch (\Exception $e) {
        Log::error('Error in profile endpoint:', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
