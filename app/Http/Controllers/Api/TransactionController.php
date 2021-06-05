<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {
    }

    public function create(TransactionRequest $request): JsonResponse
    {
        $data          = $request->all();
        $data['payer'] = auth('api')->id();

        $response = $this->transactionService->create($data);

        return response()->json([
            'data' => [
                'message'        => 'Successful transaction.',
                'transaction_id' => $response
            ]
        ]);
    }
}
