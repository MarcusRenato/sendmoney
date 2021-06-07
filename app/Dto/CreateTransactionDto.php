<?php

namespace App\Dto;

use DomainException;
use InvalidArgumentException;

class CreateTransactionDto
{
    public function __construct(
        private int $payee,
        private int $payer,
        private float $value
    ) {
    }

    public function getPayee(): int
    {
        return $this->payee;
    }

    public function getPayer(): int
    {
        return $this->payer;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'payer_id' => $this->payer,
            'payee_id' => $this->payee,
            'value'    => $this->value
        ];
    }

    public static function populate(array $params): self
    {
        self::validate($params);

        return new self(
            $params['payee'],
            $params['payer'],
            $params['value']
        );
    }

    private static function validate(array $params): void
    {
        if (! isset($params['payee'])) {
            throw new DomainException((string) json_encode(['payee' => ['Payee is required.']]), 422);
        }

        if (! isset($params['payer'])) {
            throw new DomainException((string) json_encode(['payer' => ['Payer is required.']]), 422);
        }

        if (! isset($params['value'])) {
            throw new DomainException((string) json_encode(['value' => ['Value is required.']]), 422);
        }

        if (! filter_var($params['payee'], FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException((string) json_encode(['payee' => ['Payee is not valid.']]), 422);
        }

        if (! filter_var($params['payer'], FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException((string) json_encode(['payer' => ['Payer is not valid.']]), 422);
        }

        if (! filter_var($params['value'], FILTER_VALIDATE_FLOAT)) {
            throw new InvalidArgumentException((string) json_encode(['value' => ['Value is not valid.']]), 422);
        }
    }
}
