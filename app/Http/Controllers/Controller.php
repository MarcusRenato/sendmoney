<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Sendmoney",
     *      description="Seu aplicativo para enviar dinheiro de forma fácil",
     *      @OA\Contact(
     *          email="marcusrenato@live.com"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Local"
     * )
     *
     * @OA\Tag(
     *     name="Users",
     *     description="Users"
     * )
     *
     * @OA\Tag(
     *     name="Auth",
     *     description="Authenticate"
     * )
     *
     * @OA\Tag(
     *     name="Transactions",
     *     description="Transactions"
     * )
     *
     */
}
