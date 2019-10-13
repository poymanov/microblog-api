<?php

namespace App\Repository;

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
     */
    public function getById(int $id)
    {
        return User::find($id);
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
     * Аутентификция пользователя
     *
     * @param array $credentials
     * @return User|null
     */
    public function login(array $credentials): ?User
    {
        Auth::attempt($credentials);
        return Auth::user();
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
     * Правила валидации для создания публикации
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
}
