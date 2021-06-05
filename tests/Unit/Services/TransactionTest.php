<?php

namespace Tests\Unit\Services;

use App\Dto\TransactionDto;
use App\Repositories\Contracts\TransactionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\WalletRepository;
use App\Services\TransactionService;
use Exception;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\CreatesApplication;

class TransactionTest extends TestCase
{
    use CreatesApplication;

    private TransactionRepository|MockObject $transactionRepositoryMock;
    private MockObject|WalletRepository $walletRepositoryMock;
    private MockObject|UserRepository $userRepositoryMock;
    private TransactionService $transactionService;
    private TransactionService|MockObject $transactionServiceMock;
    private array $params;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepositoryMock = $this->getMockForAbstractClass(
            TransactionRepository::class
        );
        $this->walletRepositoryMock      = $this->getMockForAbstractClass(
            WalletRepository::class
        );
        $this->userRepositoryMock        = $this->getMockForAbstractClass(
            UserRepository::class
        );

        $this->params = [
            'payer' => 1,
            'payee' => 2,
            'value' => 10
        ];

        $this->transactionService = new TransactionService(
            $this->transactionRepositoryMock,
            $this->walletRepositoryMock,
            $this->userRepositoryMock
        );
    }

    public function testIfCreateTransactionIsSuccessful(): void
    {
        $transactionDto = TransactionDto::populate($this->params);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('userExistsById')
            ->with($transactionDto->getPayee())
            ->willReturn(true);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('isUserOrdinaryById')
            ->with($transactionDto->getPayer())
            ->willReturn(true);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('balanceByUserId')
            ->with($transactionDto->getPayer())
            ->willReturn(15.0);

        $this->transactionRepositoryMock
            ->expects(self::once())
            ->method('create')
            ->with($transactionDto)
            ->willReturn(1);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('payBalance')
            ->with($transactionDto->getPayer(), $transactionDto->getValue())
            ->willReturn(true);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('receiveBalance')
            ->with($transactionDto->getPayee(), $transactionDto->getValue())
            ->willReturn(true);

        $response = $this->transactionService->create($this->params);

        self::assertEquals(1, $response);
    }

    public function testIfTransactionIsIncomplete(): void
    {
        self::expectException(Exception::class);

        $transactionDto = TransactionDto::populate($this->params);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('userExistsById')
            ->with($transactionDto->getPayee())
            ->willReturn(true);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('isUserOrdinaryById')
            ->with($transactionDto->getPayer())
            ->willReturn(true);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('balanceByUserId')
            ->with($transactionDto->getPayer())
            ->willReturn(15.0);

        $this->transactionRepositoryMock
            ->expects(self::once())
            ->method('create')
            ->with($transactionDto)
            ->willReturn(1);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('payBalance')
            ->with($transactionDto->getPayer(), $transactionDto->getValue())
            ->willReturn(false);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('receiveBalance')
            ->with($transactionDto->getPayee(), $transactionDto->getValue())
            ->willReturn(false);

        $response = $this->transactionService->create($this->params);

        self::assertNotEquals(1, $response);
    }
}
