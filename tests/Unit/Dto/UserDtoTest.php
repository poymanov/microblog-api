<?php

namespace Tests\Unit\Dto;

use App\Dto\models\UserDto;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDtoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Создание DTO из объекта пользователя
     *
     * @test
     */
    public function creating_user_dto_from_entity()
    {
        $user = factory(User::class)->create();

        $dto = new UserDto(
            $user->id,
            $user->name,
            $user->email,
            $user->created_at,
            $user->updated_at,
            $user->subscriptions_count,
            $user->subscribers_count
        );

        $this->assertEquals($user->id, $dto->getId());
        $this->assertEquals($user->name, $dto->getName());
        $this->assertEquals($user->email, $dto->getEmail());
        $this->assertEquals($user->created_at, $dto->getCreatedAt());
        $this->assertEquals($user->updated_at, $dto->getUpdatedAt());
        $this->assertEquals($user->subscriptions_count, $dto->getSubscriptionsCount());
        $this->assertEquals($user->subscribers_count, $dto->getSubscribersCount());
    }

    /**
     * Представление DTO пользователя в виде массива
     *
     * @test
     */
    public function get_user_dto_as_array()
    {
        $user = factory(User::class)->create();

        $expected = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->timestamp,
            'updated_at' => $user->updated_at->timestamp,
            'subscriptions_count' => $user->subscriptions_count,
            'subscribers_count' => $user->subscribers_count,
        ];

        $dto = new UserDto(
            $user->id,
            $user->name,
            $user->email,
            $user->created_at,
            $user->updated_at,
            $user->subscriptions_count,
            $user->subscribers_count
        );

        $this->assertIsArray($dto->toArray());
        $this->assertEquals($expected, $dto->toArray());
    }
}
