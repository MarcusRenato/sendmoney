<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @var string[]
     */
    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->payload = [
            'name'                  => 'Marcus',
            'email'                 => 'marcusrenato@email.com',
            'password'              => '123456',
            'password_confirmation' => '123456',
            'type'                  => 'comum',
            'cpf_cnpj'              => '12344578977'
        ];
    }

    public function testIfTheUserWasCreated()
    {
        $response = $this->postJson(
            '/api/v1/user',
            $this->payload
        );

        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'id' => 1
                ]
            ]);
    }

    public function testIfParamsIsValidated(): void
    {
        $data = $this->payload;
        unset($data['name']);

        $response = $this->postJson(
            '/api/v1/user',
            $data
        );

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors'  => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testIfItDoesNotAllowTheCreationOfTwoUsersWithTheSameEmail(): void
    {
        $payloadModel = $this->payload;
        unset($payloadModel['password_confirmation']);

        User::factory()->create($payloadModel);

        $response = $this->postJson(
            '/api/v1/user',
            $this->payload
        );

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors'  => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]);
    }
}
