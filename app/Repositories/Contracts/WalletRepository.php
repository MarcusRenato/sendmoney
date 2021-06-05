<?php

namespace App\Repositories\Contracts;

interface WalletRepository
{
    public function balanceByUserId(int $userId): float;

    public function receiveBalance(int $walletOwnerId, float $value): bool;

    public function payBalance(int $walletOwnerId, float $value): bool;
}
