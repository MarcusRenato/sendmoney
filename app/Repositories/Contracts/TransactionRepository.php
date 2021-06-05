<?php

namespace App\Repositories\Contracts;

use App\Dto\TransactionDto;

interface TransactionRepository
{
    public function create(TransactionDto $transactionDto): ?int;
}
