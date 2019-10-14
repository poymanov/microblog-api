<?php

namespace Tests\Unit\DtoFactories;

use App\Dto\factories\UserDtoFactory;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDtoFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Создание пользователя
     *
     * @test
     */
    public function build_user_dto()
    {
        $user = factory(User::class)->create();

        $actual = UserDtoFactory::buildUser($user);

        $this->assertEquals($user->id, $actual->getId());
        $this->assertEquals($user->name, $actual->getName());
        $this->assertEquals($user->email, $actual->getEmail());
        $this->assertEquals($user->created_at, $actual->getCreatedAt());
        $this->assertEquals($user->updated_at, $actual->getUpdatedAt());
    }
}
