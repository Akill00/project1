<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Support\Facades\Log;


class JWTAuthController extends ApiController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return $this->response(false, 'Validation errors', $validator->errors(), 422);
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    
        $token = JWTAuth::fromUser($user);
    
        return $this->response(true, 'User registered successfully', ['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->response(false, 'Unauthorized', null, 401);
        }
    
        return $this->response(true, 'Login successful', [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
    
            if (!$token) {
                return $this->response(false, 'Token not provided', null, 400);
            }
    
            JWTAuth::invalidate($token);
    
            return $this->response(true, 'Successfully logged out');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->response(false, 'Failed to logout, token might be invalid or missing', $e->getMessage(), 500);
        }
    }
    


    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
    
            if (!$token) {
                return $this->response(false, 'Token is missing or invalid', null, 400);
            }
    
            $newToken = JWTAuth::refresh();
    
            return $this->response(true, 'Token refreshed successfully', ['token' => $newToken]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->response(false, 'Failed to refresh token', $e->getMessage(), 500);
        }
    }
    


    public function profile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
    
            return $this->response(true, 'Profile retrieved successfully', $user);
        } catch (\Exception $e) {
            return $this->response(false, 'Failed to retrieve profile', $e->getMessage(), 500);
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
