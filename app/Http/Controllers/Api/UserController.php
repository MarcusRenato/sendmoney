<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    public function store(UserRequest $request): JsonResponse
    {
        $userId = $this->userService->store($request->all());

        return response()->json([
            'data' => [
                'id' => $userId
            ]
        ], 201);
    }
}
