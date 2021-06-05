<?php

namespace App\Dto;

use DomainException;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UserDto
{
    public function __construct(
        private string $name,
        private string $email,
        private string $password,
        private string $type,
        private string $cpfCnpj
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCpfCnpj(): string
    {
        return $this->cpfCnpj;
    }

    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'type'     => $this->type,
            'cpf_cnpj' => $this->cpfCnpj
        ];
    }

    public static function populate(array $params): self
    {
        self::validator($params);

        return new self(
            name: $params['name'],
            email: $params['email'],
            password: Hash::make($params['password']),
            type: strtolower($params['type']),
            cpfCnpj: str_replace([' ', '.', '-', '/'], '', $params['cpf_cnpj'])
        );
    }

    private static function validator(array $params): void
    {
        if (!isset($params['type'])) {
            throw new DomainException(json_encode(['type' => ['Type is required.']]), 422);
        }

        if (!isset($params['name'])) {
            throw new DomainException(json_encode(['name' => ['Name is required.']]), 422);
        }

        if (!isset($params['email'])) {
            throw new DomainException(json_encode(['email' => ['Email is required.']]), 422);
        }

        if (!isset($params['password'])) {
            throw new DomainException(json_encode(['password' => ['Password is required.']]), 422);
        }

        if (!isset($params['cpf_cnpj'])) {
            throw new DomainException(json_encode(['cpf_cnpj' => ['Cpf_cnpj is required.']]), 422);
        }

        if (strtolower($params['type']) !== 'comum' && strtolower($params['type']) !== 'lojista') {
            throw new InvalidArgumentException(json_encode(['type' => ['Type is not valid.']]), 422);
        }

        switch (strtolower($params['type'])) {
            case 'comum':
                if (!validatorCpf($params['cpf_cnpj'])) {
                    throw new InvalidArgumentException(json_encode(['cpf_cnpj' => ['Cpf is not valid.']]), 422);
                }
                break;
            case 'lojista':
                if (!validatorCnpj($params['cpf_cnpj'])) {
                    throw new InvalidArgumentException(json_encode(['cpf_cnpj' => ['Cnpj is not valid.']]), 422);
                }
                break;
        }

        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(json_encode(['email' => ['Email is not valid.']]), 422);
        }
    }
}
