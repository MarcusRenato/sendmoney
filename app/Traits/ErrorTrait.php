<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

trait ErrorTrait
{
    public function errorException(string $message, int $code)
    {
        throw new HttpResponseException(
            new Response(
                json_encode([
                    'message' => 'Error',
                    'errors'  => json_decode($message)
                ]),
                $code,
                ['content-type' => 'application/json']
            )
        );
    }
}
