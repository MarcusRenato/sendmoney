<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    private User $userOrdinary;
    private User $userMerchant;
    private array $headersOrdinaryUser;
    private User $otherUserOrdinary;

    public function setUp(): void
    {
        parent::setUp();

        $payloadOrdinary = [
            'name'     => 'Marcus',
            'email'    => 'marcus@email.com',
            'password' => Hash::make('123456'),
            'type'     => 'comum',
            'cpf_cnpj' => '12344578966'
        ];

        $payloadMerchant = [
            'name'     => 'Lojinha da Esquina',
            'email'    => 'esquina@email.com',
            'password' => Hash::make('123456'),
            'type'     => 'lojista',
            'cpf_cnpj' => '91456514000101'
        ];

        $payloadOtherUserOrdinary = [
            'name'     => 'Renato',
            'email'    => 'renato@email.com',
            'password' => Hash::make('123456'),
            'type'     => 'comum',
            'cpf_cnpj' => '12344578977'
        ];

        $this->userOrdinary = User::factory()->create($payloadOrdinary);
        $this->userOrdinary->wallet()->create([
            'value' => 15
        ]);

        $this->otherUserOrdinary = User::factory()->create($payloadOtherUserOrdinary);
        $this->otherUserOrdinary->wallet()->create();

        $this->userMerchant = User::factory()->create($payloadMerchant);
        $this->userMerchant->wallet()->create();

        $tokenUserOrdinary = auth('api')->tokenById($this->userOrdinary->id);

        $this->headersOrdinaryUser = [
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => "bearer {$tokenUserOrdinary}"
        ];

        Queue::fake();
    }

    public function testIfTransactionWorkCorrectly(): void
    {
        $payload = [
            'payee' => $this->userMerchant->id,
            'value' => 10.0
        ];

        $response = $this
            ->withHeaders($this->headersOrdinaryUser)
            ->postJson('/api/v1/transaction', $payload);

        $response->assertSuccessful()
            ->assertJson([
                'data' => [
                    'message'        => 'Successful transaction.',
                    'transaction_id' => 1
                ]
            ]);
    }

    public function testIfUserOrdinaryTriesToTransactionToUserOrdinary()
    {
        $payload = [
            'payee' => $this->otherUserOrdinary->id,
            'value' => 2
        ];

        $response = $this
            ->withHeaders($this->headersOrdinaryUser)
            ->postJson('/api/v1/transaction', $payload);

        $response->assertSuccessful()
            ->assertJson([
                'data' => [
                    'message'        => 'Successful transaction.',
                    'transaction_id' => 1
                ]
            ]);
    }

    public function testIfTheMerchantUserTriesToTransfer(): void
    {
        $payload = [
            'payee' => $this->userOrdinary->id,
            'value' => 10
        ];

        $tokenUserMerchant = auth('api')->tokenById($this->userMerchant->id);

        $headers = [
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => "bearer {$tokenUserMerchant}"
        ];

        $response = $this
            ->withHeaders($headers)
            ->postJson('/api/v1/transaction', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error',
                'errors'  => [
                    'message' => 'You cannot transfer.'
                ]
            ]);
    }

    public function testIfWalletValueIsLessThanTransferValue(): void
    {
        $payload = [
            'payee' => $this->userMerchant->id,
            'value' => 100.0
        ];

        $response = $this
            ->withHeaders($this->headersOrdinaryUser)
            ->postJson('/api/v1/transaction', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error',
                'errors'  => [
                    'message' => 'You do not have enough value in your wallet to carry out the transfer.'
                ]
            ]);
    }

    public function testIfValueTranferIsLessThanOrEqualToZero(): void
    {
        $payload = [
            'payee' => $this->userMerchant->id,
            'value' => -15
        ];

        $response = $this
            ->withHeaders($this->headersOrdinaryUser)
            ->postJson('/api/v1/transaction', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error',
                'errors'  => [
                    'message' => 'Value must be greater than 0.'
                ]
            ]);
    }

    public function testIfTheUserTriesToTransferToHimselfItMustFail(): void
    {
        $payload = [
            'payee' => $this->userOrdinary->id,
            'value' => 10
        ];

        $response = $this
            ->withHeaders($this->headersOrdinaryUser)
            ->postJson('/api/v1/transaction', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error',
                'errors'  => [
                    'message' => 'You cannot make a transfer to yourself.'
                ]
            ]);
    }

    public function testIfUserTriesToTransferToNonExistentUser(): void
    {
        $payload = [
            'payee' => 1000,
            'value' => 10
        ];

        $response = $this
            ->withHeaders($this->headersOrdinaryUser)
            ->postJson('/api/v1/transaction', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error',
                'errors'  => [
                    'message' => "User '{$payload['payee']}' not found."
                ]
            ]);
    }
}
