<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testIfTheUserWasCreated()
    {
        $response = $this->postJson(
            '/api/v1/user',
            [
                'name'                  => 'Marcus',
                'email'                 => 'marcusrenato@email.com',
                'password'              => '123456',
                'password_confirmation' => '123456',
                'type'                  => 'comum',
                'cpf_cnpj'              => '12344578977'
            ]
        );

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1
                ]
            ]);
    }

    public function testIfParamsIsValidated(): void
    {
        $response = $this->postJson(
            '/api/v1/user',
            [
                'email'                 => 'marcus@email.com',
                'password'              => '123456',
                'password_confirmation' => '123456',
                'type'                  => 'comum',
                'cpf_cnpj'              => '12344578966'
            ]
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
        $payload = [
            'name'                  => 'Marcus',
            'email'                 => 'marcus@email.com',
            'password'              => '123456',
            'password_confirmation' => '123456',
            'type'                  => 'comum',
            'cpf_cnpj'              => '12344578966'
        ];

        $payloadModel = $payload;
        unset($payloadModel['password_confirmation']);

        User::factory()->create($payloadModel);

        $response = $this->postJson(
            '/api/v1/user',
            $payload
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
