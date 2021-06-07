<?php

namespace App\Jobs;

use App\Dto\TransactionDto;
use App\Mail\NotificationTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private TransactionDto $transactionDto
    ) {
    }

    public function handle(): void
    {
        $url = 'http://o4d9z.mocklab.io/notify';

        $response = (array) json_decode(file_get_contents($url));

        if ($response['message'] === 'Success') {
            // Envio do e-mail...
            Mail::send(new NotificationTransaction($this->transactionDto));
        }
    }
}
