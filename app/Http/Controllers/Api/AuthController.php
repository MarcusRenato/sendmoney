<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'     => 'required|email|string',
            'password'  => 'required|string|min:3'
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
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
