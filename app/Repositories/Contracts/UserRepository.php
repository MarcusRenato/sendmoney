<?php

namespace App\Repositories\Contracts;

use App\Dto\UserDto;

interface UserRepository
{
    public function store(UserDto $userDto): int;

    public function userExists(string $email, string $cpfCnpj): bool;
}
