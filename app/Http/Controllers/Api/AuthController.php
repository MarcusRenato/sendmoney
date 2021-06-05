<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'data' => [
                    'message' => 'Incorrect email and/or password.'
                ]
            ], 401);
        }

        return response()->json([
            'data' => [
                'token'      => $token,
                'token_type' => 'bearer'
            ]
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json([
            'data' => [
                'message' => 'Successfully logged out.'
            ]
        ]);
    }
}
