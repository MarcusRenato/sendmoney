<?php

namespace App\Repositories\Contracts;

use App\Dto\CreateTransactionDto;
use App\Dto\TransactionDto;

interface TransactionRepository
{
    public function create(CreateTransactionDto $transactionDto): int;

    public function getById(int $transactionId): TransactionDto;
}
