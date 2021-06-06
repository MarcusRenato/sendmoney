<?php

namespace App\Repositories\Eloquent;

use App\Dto\CreateTransactionDto;
use App\Dto\TransactionDto;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepository;
use Exception;

class TransactionRepositoryFromEloquent implements TransactionRepository
{
    public function __construct(
        private Transaction $transaction
    ) {
    }

    public function create(CreateTransactionDto $transactionDto): int
    {
        $transaction = $this->transaction->create($transactionDto->toArray());

        if (! $transaction instanceof Transaction) {
            throw new Exception(
                'An error has occurred and the transaction cannot be completed.',
                500
            );
        }

        return $transaction->id;
    }

    public function getById(int $transactionId): TransactionDto
    {
        $transaction = $this->transaction
            ->select(
                'payer.name         AS payerName',
                'payee.name         AS payeeName',
                'payee.email        AS payeeEmail',
                'transactions.value AS value'
            )
            ->join('users AS payer', 'transactions.payer_id', '=', 'payer.id')
            ->join('users AS payee', 'transactions.payee_id', '=', 'payee.id')
            ->first()
            ->toArray();

        return TransactionDto::populate($transaction);
    }
}
