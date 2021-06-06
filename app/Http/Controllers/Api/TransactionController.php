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

    /**
     * @OA\Post(
     *      path="/transaction",
     *      operationId="createTransaction",
     *      tags={"Transactions"},
     *      summary="Create a new transaction",
     *      description="Returns transaction id",
     *      @OA\Parameter(
     *          description="application/json",
     *          name="Accept",
     *          in="header"
     *      ),
     *      @OA\Parameter(
     *          description="application/json",
     *          name="Content-Type",
     *          in="header"
     *      ),
     *     @OA\Parameter(
     *          description="bearer {token}",
     *          example="bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9....",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Transaction"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="json",
     *              example={
     *                  "data": {
     *                      "message": "Successful transaction.",
     *                      "transaction_id": 1
     *                  }
     *              }
     *          )
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Data Input is invalid",
     *          @OA\Header(
     *              header="Content-Type",
     *              @OA\Schema(
     *                  type="application/json"
     *              )
     *          ),
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *                   "message": "The given data was invalid.",
     *                   "errors": {
     *                      "payee": {
     *                          "The payee field is required."
     *                       }
     *                   }
     *              }
     *          )
     *      )
     * )
     */
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
