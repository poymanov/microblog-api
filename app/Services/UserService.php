<?php

namespace App\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\LoginResponseDto;
use App\Dto\ResponseDtoInterface;
use App\Exceptions\UnauthorizedException;
use App\Repository\UserRepository;
use App\User;

/**
 * Class UserService
 * @package App\Services
 *
 * Сервис управления пользователями
 */
class UserService extends BaseService
{
    /** @var UserRepository Репозиторий для работы с публикациями*/
    private $repository;

    /**
     * PostService constructor.
     */
    public function __construct()
    {
        $this->repository = app(UserRepository::class);
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
        $this->repository->validateData($data, $this->repository->getCreatingValidationRules());
        $this->repository->create($data);

        return SuccessfulResponseDtoFactory::buildSuccessfulSignup();
    }

    /**
     * Аутентификации пользователя
     *
     * @param array $data
     * @return LoginResponseDto
     * @throws \App\Exceptions\ValidationException
     */
    public function loginUser(array $data): LoginResponseDto
    {
        $this->repository->validateData($data, $this->repository->getLoginValidationRules());

        // Попытка авторизации пользователя
        $user = $this->repository->login($data);

        $this->throwExceptionIfNull($user, UnauthorizedException::class);

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
