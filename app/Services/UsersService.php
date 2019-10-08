<?php

namespace App\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\LoginResponseDto;
use App\Dto\ResponseDtoInterface;
use App\Exceptions\UnauthorizedException;
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

    /**
     * Регистрация пользователя
     *
     * @param array $data
     * @return mixed
     * @throws \App\Exceptions\ValidationException
     */
    public function registerUser(array $data): ResponseDtoInterface
    {
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        $this->repository->validateData($data, $validationRules);
        $this->repository->create($data);

        return SuccessfulResponseDtoFactory::buildSuccessfulSignup();
    }

    /**
     * Аутентификации пользователя
     *
     * @param array $data
     * @return LoginResponseDto
     * @throws UnauthorizedException
     * @throws \App\Exceptions\ValidationException
     */
    public function loginUser(array $data): LoginResponseDto
    {
        $validationRules = [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ];

        $this->repository->validateData($data, $validationRules);

        // Попытка авторизации пользователя
        $user = $this->repository->login($data);

        if (is_null($user)) {
            throw new UnauthorizedException();
        }

        // Создание токен авторизации пользователя
        $token = $this->repository->createToken($user);

        return SuccessfulResponseDtoFactory::buildSuccessfulLogin($token);
    }

    /**
     * Завершение сеанса пользователя
     *
     * @return ResponseDtoInterface
     */
    public function logoutUser(): ResponseDtoInterface
    {
        $this->repository->logout();

        return SuccessfulResponseDtoFactory::buildSuccessfulLogout();
    }
}
