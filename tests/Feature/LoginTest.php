<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $payloadUser = [
            'name'     => 'Marcus',
            'email'    => 'marcus@email.com',
            'password' => Hash::make('123456'),
            'type'     => 'comum',
            'cpf_cnpj' => '12344578966'
        ];

        $this->user = User::factory()->create($payloadUser);
    }

    public function testIfLoginWorksCorrectly(): void
    {
        $payload = [
            'email'    => 'marcus@email.com',
            'password' => '123456'
        ];

        $response = $this
            ->withHeaders([
                'Content-type' => 'application/json',
                'Accept'       => 'application/json'
            ])
            ->postJson('/api/v1/auth/login', $payload);

        $response->assertSuccessful();
    }

    public function testIfLoginWithInvalidCredentialsShouldFail(): void
    {
        $payload = [
            'email'    => 'marcus@email.com',
            'password' => 'password'
        ];

        $response = $this
            ->withHeaders([
                'Content-type' => 'application/json',
                'Accept'       => 'application/json'
            ])
            ->postJson('/api/v1/auth/login', $payload);

        $response->assertUnauthorized()
            ->assertJson([
                'data' => [
                    'message' => 'Incorrect email and/or password.'
                ]
            ]);
    }

    public function testIfLogoutShouldWork(): void
    {
        $token = auth('api')->tokenById($this->user->id);

        $response = $this
            ->withHeaders([
                'Content-type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => "bearer {$token}"
            ])
            ->postJson('/api/v1/auth/logout');

        $response->assertSuccessful()
            ->assertJson([
                'data' => [
                    'message' => 'Successfully logged out.'
                ]
            ]);
    }

    public function testIfLogoutWithoutTokenShouldFail(): void
    {
        $response = $this
            ->withHeaders([
                'Content-type'  => 'application/json',
                'Accept'        => 'application/json'
            ])
            ->postJson('/api/v1/auth/logout');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}
