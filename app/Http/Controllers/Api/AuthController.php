<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/auth/login",
     *      operationId="loginUser",
     *      tags={"Auth"},
     *      summary="Login",
     *      description="Returns user token",
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
     *          @OA\JsonContent(
     *              example={
     *                  "email": "email@email.com",
     *                  "password": "123456"
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Header(
     *              header="Content-Type",
     *              @OA\Schema(
     *                  type="application/json"
     *              )
     *          ),
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *                  "data": {
     *                      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9....",
     *                      "token_type": "bearer"
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
     *                      "email": {
     *                          "The email must be a valid email address."
     *                       }
     *                   }
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\Header(
     *              header="Content-Type",
     *              @OA\Schema(
     *                  type="application/json"
     *              )
     *          ),
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *                  "message": "Incorrect email and/or password"
     *              }
     *          )
     *      )
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/auth/logout",
     *      operationId="logoutUser",
     *      tags={"Auth"},
     *      summary="Logout",
     *      description="Invalid User Token.",
     *      @OA\Parameter(
     *          description="application/json",
     *          example="application/json",
     *          name="Accept",
     *          in="header"
     *      ),
     *      @OA\Parameter(
     *          description="application/json",
     *          name="Content-Type",
     *          example="application/json",
     *          in="header"
     *      ),
     *     @OA\Parameter(
     *          description="bearer {token}",
     *          example="bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9....",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation.",
     *          @OA\Header(
     *              header="Content-Type",
     *              @OA\Schema(
     *                  type="application/json"
     *              )
     *          ),
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *                  "data": {
     *                      "message": "Successfully logged out."
     *                  }
     *              }
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\Header(
     *              header="Content-Type",
     *              @OA\Schema(
     *                  type="application/json"
     *              )
     *          ),
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *                  "message": "Unauthenticated."
     *              }
     *          )
     *      )
     * )
     */
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
