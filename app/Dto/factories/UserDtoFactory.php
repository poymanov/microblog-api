<?php

namespace App\Dto\factories;

use App\Dto\models\UserDto;
use App\User;

/**
 * Class UserDtoFactory
 * @package App\Dto\factories
 *
 * Фабрика для создания DTO объектов пользователей
 */
class UserDtoFactory
{
    /**
     * Создание DTO пользователя
     *
     * @param User $user
     * @return UserDto
     */
    public static function buildUser(User $user): UserDto
    {
        return new UserDto(
            $user->id,
            $user->name,
            $user->email,
            $user->created_at,
            $user->updated_at,
            $user->subscriptions_count,
            $user->subscribers_count
        );
    }
}
