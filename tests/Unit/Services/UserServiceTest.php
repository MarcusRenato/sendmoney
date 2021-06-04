<?php

namespace Tests\Unit\Services;

use App\Repositories\Contracts\UserRepository;
use App\Services\UserService;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\CreatesApplication;

class UserServiceTest extends TestCase
{
    use CreatesApplication;

    private UserService $userService;
    private MockObject|UserRepository $userRepository;
    private array $params;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->getMockForAbstractClass(UserRepository::class);

        $this->userService = new UserService($this->userRepository);

        $this->params = [
            'name'     => 'Marcus',
            'email'    => 'marcus@email.com',
            'password' => '123456',
            'type'     => 'comum',
            'cpf_cnpj' => '12345678966'
        ];
    }

    public function testIfTheUserWasCreated(): void
    {
        $this->userRepository
            ->expects(self::once())
            ->method('store')
            ->willReturn(1);

        $this->userRepository
            ->expects(self::once())
            ->method('userExists')
            ->willReturn(false);

        $return = $this->userService->store($this->params);

        self::assertEquals(1, $return);
    }
}
