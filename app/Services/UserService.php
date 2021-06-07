<?php

namespace App\Services;

use App\Dto\UserDto;
use App\Repositories\Contracts\UserRepository;
use App\Traits\ErrorTrait;
use Exception;

class UserService
{
    use ErrorTrait;

    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function store(array $params): int
    {
        try {
            $userDto = UserDto::populate($params);

            if ($this->userExists($userDto->getEmail(), $userDto->getCpfCnpj())) {
                throw new Exception(
                    (string) json_encode(['user' => 'User already exists.']),
                    422
                );
            }

            return $this->userRepository->store($userDto);
        } catch (Exception $e) {
            $this->errorException($e->getMessage(), $e->getCode());
            return 0;
        }
    }

    private function userExists(string $email, string $cpfCnpj): bool
    {
        return $this->userRepository->userExists($email, $cpfCnpj);
    }
}
