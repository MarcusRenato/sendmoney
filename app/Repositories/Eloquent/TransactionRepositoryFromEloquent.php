<?php

namespace App\Repositories\Eloquent;

use App\Dto\TransactionDto;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepository;

class TransactionRepositoryFromEloquent implements TransactionRepository
{
    public function __construct(
        private Transaction $transaction
    ) {
    }

    public function create(TransactionDto $transactionDto): ?int
    {
        $transaction = $this->transaction->create($transactionDto->toArray());

        return $transaction instanceof Transaction ? $transaction->id : null;
    }
}
