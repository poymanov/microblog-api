<?php

namespace App\Services;

use App\Repository\UsersRepository;
use App\User;

/**
 * Class UsersService
 * @package App\Services
 *
 * Сервис управления пользователями
 */
class UsersService
{
    /** @var UsersRepository Репозиторий для работы с публикациями*/
    private $repository;

    /**
     * PostsService constructor.
     */
    public function __construct()
    {
        $this->repository = app(UsersRepository::class);
    }

    /**
     * Получение пользователя по id
     *
     * @param int $id
     * @return User|null
     */
    public function getById(int $id): ?User
    {
        return $this->repository->getById($id);
    }
}
