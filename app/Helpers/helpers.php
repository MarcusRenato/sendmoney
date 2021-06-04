<?php

function validatorCpf(string $value): bool
{
    $value = str_replace(['-', ' ', '.'], [''], $value);

    if (strlen($value) !== 11) {
        return false;
    }

    return true;
}

function validatorCnpj (string $value): bool
{
    $value = str_replace(['-', ' ', '.', '/'], [''], $value);

    if (strlen($value) !== 14) {
        return false;
    }

    return true;
}
