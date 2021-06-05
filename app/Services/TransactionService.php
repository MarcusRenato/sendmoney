<?php

namespace App\Services;

use App\Dto\TransactionDto;
use App\Repositories\Contracts\TransactionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\WalletRepository;
use App\Traits\ErrorTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    use ErrorTrait;

    private const BASE_URL = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

    public function __construct(
        private TransactionRepository $transactionRepository,
        private WalletRepository $walletRepository,
        private UserRepository $userRepository
    ) {
    }

    public function create(array $params): int
    {
        try {
            $transactionDto = TransactionDto::populate($params);

            $this->checkTransactionIsAble($transactionDto);

            $walletPayer = $this->balanceInMyWalletByUserId($transactionDto->getPayer());

            if ($walletPayer < $transactionDto->getValue()) {
                throw new Exception(
                    json_encode(
                        ['message' => 'You do not have enough value in your wallet to carry out the transfer.']
                    ),
                    422
                );
            }

            if (! $this->checkTransactionIsApproved()) {
                throw new Exception(
                    json_encode(
                        ['message' => 'Transaction not approved.']
                    ),
                    422
                );
            }

            DB::beginTransaction();
            $transactionId = $this->transactionRepository->create($transactionDto);

            $responsePayInsert = $this->walletRepository->payBalance(
                $transactionDto->getPayer(),
                $transactionDto->getValue()
            );

            $responseReceiveInsert = $this->walletRepository->receiveBalance(
                $transactionDto->getPayee(),
                $transactionDto->getValue()
            );

            if (! $responsePayInsert  && ! $responseReceiveInsert) {
                throw new Exception(json_encode(
                        ['message' => 'An error has occurred and the transaction cannot be completed.']
                    ),
                    422
                );
            }

            DB::commit();

            return $transactionId;
        } catch (Exception $e) {
            DB::rollBack();
            $this->errorException($e->getMessage(), $e->getCode());
            return 0;
        }
    }

    private function checkIfUserExists(int $id): bool
    {
        return $this->userRepository->userExistsById($id);
    }

    private function balanceInMyWalletByUserId(int $userId): float
    {
        return $this->walletRepository->balanceByUserId($userId);
    }

    private function isUserOrdinary(int $userId): bool
    {
        return $this->userRepository->isUserOrdinaryById($userId);
    }

    private function checkTransactionIsAble(TransactionDto $transactionDto): void
    {
        if (! $this->isUserOrdinary($transactionDto->getPayer())) {
            throw new Exception(
                json_encode(['message' => 'You cannot transfer.']),
                422
            );
        }

        if ($transactionDto->getPayer() === $transactionDto->getPayee()) {
            throw new Exception(
                json_encode(['message' => 'You cannot make a transfer to yourself.']),
                422
            );
        }

        if (! $this->checkIfUserExists($transactionDto->getPayee())) {
            throw new Exception(
                json_encode(['message' => "User '{$transactionDto->getPayee()}' not found."]),
                422
            );
        }

        if ($transactionDto->getValue() <= 0) {
            throw new Exception(
                json_encode(['message' => 'Value must be greater than 0.']),
                422
            );
        }
    }

    private function checkTransactionIsApproved(): bool
    {
        $response = (array) json_decode(file_get_contents(self::BASE_URL));

        if ($response['message'] !== 'Autorizado') {
            return false;
        }

        return true;
    }
}
