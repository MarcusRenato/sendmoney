<?php

namespace App\Repositories\Eloquent;

use App\Dto\UserDto;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Exception;

class UserRepositoryFromEloquent implements UserRepository
{
    public function __construct(
        private User $user
    ) {
    }

    public function store(UserDto $userDto): int
    {
        $user = $this->user->create($userDto->toArray());

        $user->wallet()->create();

        return $user->id;
    }

    public function userExists(string $email, string $cpfCnpj): bool
    {
        $user = $this->user->where('email', $email)->orWhere('cpf_cnpj', $cpfCnpj)->first();

        return $user instanceof User;
    }

    public function userExistsById(int $id): bool
    {
        $user = $this->user->find($id);

        return $user instanceof User;
    }

    public function isUserOrdinaryById(int $id): bool
    {
        $user = $this->user->find($id);

        if (! $user instanceof User) {
            throw new Exception("User {$id} not found.", 422);
        }

        return $user->type === 'comum';
    }
}
