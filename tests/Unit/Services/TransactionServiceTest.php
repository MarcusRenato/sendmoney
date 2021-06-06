<?php

namespace Tests\Unit\Services;

use App\Dto\CreateTransactionDto;
use App\Dto\TransactionDto;
use App\Jobs\SendNotification;
use App\Repositories\Contracts\TransactionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\WalletRepository;
use App\Services\TransactionService;
use Exception;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\CreatesApplication;

class TransactionServiceTest extends TestCase
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
        $createTransactionDto = CreateTransactionDto::populate($this->params);
        $transactionDto = TransactionDto::populate([
            'payerName'  => 'Marcus',
            'payeeName'  => 'Renato',
            'payeeEmail' => 'renato@email.com',
            'value'      => 10
        ]);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('userExistsById')
            ->with($createTransactionDto->getPayee())
            ->willReturn(true);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('isUserOrdinaryById')
            ->with($createTransactionDto->getPayer())
            ->willReturn(true);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('balanceByUserId')
            ->with($createTransactionDto->getPayer())
            ->willReturn(15.0);

        $this->transactionRepositoryMock
            ->expects(self::once())
            ->method('create')
            ->with($createTransactionDto)
            ->willReturn(1);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('payBalance')
            ->with($createTransactionDto->getPayer(), $createTransactionDto->getValue())
            ->willReturn(true);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('receiveBalance')
            ->with($createTransactionDto->getPayee(), $createTransactionDto->getValue())
            ->willReturn(true);

        $this->transactionRepositoryMock
            ->expects(self::once())
            ->method('getById')
            ->willReturn($transactionDto);

        Queue::fake();

        SendNotification::dispatch($transactionDto);

        $response = $this->transactionService->create($this->params);

        Queue::assertPushed(SendNotification::class);

        self::assertEquals(1, $response);
    }

    public function testIfTransactionIsIncomplete(): void
    {
        self::expectException(Exception::class);

        $createTransactionDto = CreateTransactionDto::populate($this->params);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('userExistsById')
            ->with($createTransactionDto->getPayee())
            ->willReturn(true);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('isUserOrdinaryById')
            ->with($createTransactionDto->getPayer())
            ->willReturn(true);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('balanceByUserId')
            ->with($createTransactionDto->getPayer())
            ->willReturn(15.0);

        $this->transactionRepositoryMock
            ->expects(self::once())
            ->method('create')
            ->with($createTransactionDto)
            ->willReturn(1);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('payBalance')
            ->with($createTransactionDto->getPayer(), $createTransactionDto->getValue())
            ->willReturn(false);

        $this->walletRepositoryMock
            ->expects(self::once())
            ->method('receiveBalance')
            ->with($createTransactionDto->getPayee(), $createTransactionDto->getValue())
            ->willReturn(false);

        $response = $this->transactionService->create($this->params);

        self::assertNotEquals(1, $response);
    }
}
