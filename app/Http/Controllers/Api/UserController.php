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

    /**
     * @OA\Post(
     *      path="/user",
     *      operationId="storeUSer",
     *      tags={"Users"},
     *      summary="Store new User",
     *      description="Returns user id",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/User"),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\MediaType(mediaType="application/json", example={"data": {"id": 1}})
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
     *                      "name": {
     *                          "The name field is required."
     *                       }
     *                   }
     *              }
     *          )
     *      )
     * )
     */
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
