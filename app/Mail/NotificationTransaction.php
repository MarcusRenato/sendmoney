<?php

namespace App\Mail;

use App\Dto\TransactionDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationTransaction extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public TransactionDto $transactionDto
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.index')
            ->subject('Você recebeu uma transferência')
            ->from('no-reply@sendmoney.com', 'Sendmoney')
            ->to($this->transactionDto->getPayeeEmail());
    }
}
