<?php

namespace App\Repositories\Eloquent;

use App\Dto\UserDto;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;

class UserRepositoryFromEloquent implements UserRepository
{
    public function store(UserDto $userDto): int
    {
        $user = User::create($userDto->toArray());

        $user->wallet()->create();

        return $user->id;
    }

    public function userExists(string $email, string $cpfCnpj): bool
    {
        $user = User::where('email', $email)->orWhere('cpf_cnpj', $cpfCnpj)->first();

        return $user instanceof User;
    }
}
