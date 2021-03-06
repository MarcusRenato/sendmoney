<?php

namespace App\Repositories\Eloquent;

use App\Models\Wallet;
use App\Repositories\Contracts\WalletRepository;
use Exception;

class WalletRepositoryFromDoctrine implements WalletRepository
{
    public function __construct(
        private Wallet $wallet
    ) {
    }

    public function balanceByUserId(int $userId): float
    {
        $wallet = $this->wallet->where('user_id', $userId)->first();

        if (! $wallet instanceof Wallet) {
            $this->throwWalletNotFoundException($userId);
        }

        return $wallet->value;
    }

    public function receiveBalance(int $walletOwnerId, float $value): bool
    {
        $wallet = $this->wallet->where('user_id', $walletOwnerId)->first();

        if (! $wallet instanceof Wallet) {
            $this->throwWalletNotFoundException($walletOwnerId);
        }

        return $wallet->update([
            'value' => $wallet->value + $value
        ]);
    }

    public function payBalance(int $walletOwnerId, float $value): bool
    {
        $wallet = $this->wallet->where('user_id', $walletOwnerId)->first();

        if (! $wallet instanceof Wallet) {
            $this->throwWalletNotFoundException($walletOwnerId);
        }

        return $wallet->update([
            'value' => $wallet->value - $value
        ]);
    }

    /** @throws Exception */
    private function throwWalletNotFoundException(int $walletOwnerId): void
    {
        throw new Exception(
            (string) json_encode([
                'message' => "Wallet for user '{$walletOwnerId}' not found."
            ]),
            422
        );
    }
}
