<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

trait ErrorTrait
{
    public function errorException(string $message, int $code): void
    {
        throw new HttpResponseException(
            new Response(
                (string) json_encode([
                    'message' => 'Error',
                    'errors'  => json_decode($message)
                ]),
                $code,
                ['content-type' => 'application/json']
            )
        );
    }
}
