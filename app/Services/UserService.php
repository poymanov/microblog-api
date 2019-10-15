<?php

namespace App\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\LoginResponseDto;
use App\Dto\models\UserDto;
use App\Dto\ResponseDtoInterface;
use App\Exceptions\UnauthorizedException;
use App\Repository\UserRepository;

/**
 * Class UserService
 * @package App\Services
 *
 * Сервис управления пользователями
 */
class UserService extends BaseService
{
    /** @var UserRepository Репозиторий для работы с публикациями */
    private $repository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * Получение пользователя по id
     *
     * @param int $id
     * @return UserDto
     * @throws \App\Exceptions\NotFoundException
     */
    public function getById(int $id): UserDto
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
     * @throws UnauthorizedException
     * @throws \App\Exceptions\ValidationException
     */
    public function loginUser(array $data): LoginResponseDto
    {
        $this->repository->validateData($data, $this->repository->getLoginValidationRules());

        // Попытка авторизации пользователя
        $user = $this->repository->login($data);

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

    /**
     * Редактирование пользователя
     *
     * @param array $data
     * @param int $id
     * @return UserDto
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\ValidationException
     */
    public function updateUser(array $data, int $id): UserDto
    {
        $this->repository->validateData($data, $this->repository->getUpdateValidationRules());

        $this->repository->update($data, $id);

        return $this->getById($id);
    }
}
