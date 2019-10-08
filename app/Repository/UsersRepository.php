<?php

namespace App\Repository;

use App\User;

/**
 * Class UsersRepository
 * @package App\Repository
 *
 * Репозиторий для управления пользователя
 */
class UsersRepository
{
    /**
     * Получение пользователя по id
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return User::find($id);
    }
}
