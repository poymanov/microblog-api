<?php

namespace App\Repository;

use App\Dto\factories\UserDtoFactory;
use App\Dto\models\UserDto;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\PersonalAccessTokenResult;

/**
 * Class UserRepository
 * @package App\Repository
 *
 * Репозиторий для управления пользователя
 */
class UserRepository extends AbstractRepository
{
    /**
     * Получение пользователя по id
     *
     * @param int $id
     * @return mixed
     * @throws NotFoundException
     */
    public function getById(int $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        return UserDtoFactory::buildUser($user);
    }

    /**
     * Получение подписок пользователя
     *
     * @param int $id
     * @return UserDto[]
     * @throws NotFoundException
     */
    public function getSubscriptions(int $id): array
    {
        $dto = [];

        $user = User::find($id);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        foreach ($user->subscriptions as $user) {
            $dto[] = UserDtoFactory::buildUser($user);
        }

        return $dto;
    }

    /**
     * Получение подписчиков пользователя
     *
     * @param int $id
     * @return UserDto[]
     * @throws NotFoundException
     */
    public function getSubscribers(int $id): array
    {
        $dto = [];

        $user = User::find($id);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        foreach ($user->subscribers as $user) {
            $dto[] = UserDtoFactory::buildUser($user);
        }

        return $dto;
    }

    /**
     * Создание пользователя
     *
     * @param array $data
     */
    public function create(array $data): void
    {
        $data['password'] = Hash::make($data['password']);

        User::create($data);
    }

    /**
     * Редактирование пользователя
     *
     * @param array $data
     * @param int $id
     * @throws NotFoundException
     */
    public function update(array $data, int $id): void
    {
        $user = User::find($id);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
    }

    /**
     * Аутентификция пользователя
     *
     * @param array $credentials
     * @return User
     * @throws UnauthorizedException
     */
    public function login(array $credentials): User
    {
        Auth::attempt($credentials);

        $user = Auth::user();

        if (is_null($user)) {
            throw new UnauthorizedException();
        }

        return $user;
    }

    /**
     * Создание токена доступа для пользователя
     *
     * @param User $user
     * @return PersonalAccessTokenResult
     */
    public function createToken(User $user): PersonalAccessTokenResult
    {
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addHour();
        $token->save();

        return $tokenResult;
    }

    /**
     * Завершение сеанса пользователя
     */
    public function logout(): void
    {
        Auth::user()->token()->revoke();
    }

    /**
     * Правила валидации для создания публикации
     *
     * @return array
     */
    public function getCreatingValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    /**
     * Правила валидации для логина пользователя
     *
     * @return array
     */
    public function getLoginValidationRules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Правила валидации для редактирования пользователя
     *
     * @return array
     */
    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }
}
