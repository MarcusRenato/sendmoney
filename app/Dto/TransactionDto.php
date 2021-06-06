<?php

namespace App\Dto;

class TransactionDto
{
    public function __construct(
        private string $payerName,
        private string $payeeName,
        private string $payeeEmail,
        private float $value
    ) {
    }

    public function getPayerName(): string
    {
        return $this->payerName;
    }

    public function getPayeeName(): string
    {
        return $this->payeeName;
    }

    public function getPayeeEmail(): string
    {
        return $this->payeeEmail;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public static function populate(array $params): self
    {
        return new self(...$params);
    }
}
