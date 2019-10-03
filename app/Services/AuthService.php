<?php

namespace App\Services;

use App\Dto\ErrorResponseDto;
use App\Dto\factories\ErrorResponseDtoFactory;
use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\factories\ValidationDtoFactory;
use App\Dto\ResponseDto;
use App\Dto\ValidationDto;
use App\User;
use Carbon\Carbon;

/**
 * Class AuthService
 * @package App\Services
 *
 * Сервис для управления авторизацией пользователей
 */
class AuthService extends BaseService
{
    /**
     * Создание токена доступа для пользователя
     *
     * @param User $user
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function createToken(User $user)
    {
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addHour();
        $token->save();

        return $tokenResult;
    }

    /**
     * Данные для ответа в json: успешная авторизация
     *
     * @param $token
     * @return array
     */
    public function getSuccessAuthResponseData($token)
    {
        return [
            'data' => [
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString(),
            ]
        ];
    }

    /**
     * Данные для ответа в json: успешное завершение сеанса авторизации пользователя
     *
     * @return array
     */
    public function getSuccessfullyLogoutResponseData()
    {
        return [
            'data' => [
                'message' => trans('responses.successfully_logout.message'),
            ]
        ];
    }

    /**
     * Данные для ответа в json: успешное завершения сеанса регистрации пользователя
     *
     * @return ResponseDto
     */
    public function getSuccessfullySignupResponseData(): ResponseDto
    {
        return SuccessfulResponseDtoFactory::buildSuccessfulSignup();
    }

    /**
     * Данные для ответа в json: данные переданы в неверном формате или не указаны
     *
     * @param array $errors
     * @return ValidationDto
     */
    public function getFailedValidationResponseData(array $errors): ValidationDto
    {
        return ValidationDtoFactory::buildFailed($errors);
    }

    /**
     * Данные для ответа в json: переданы неправильные данные для авторизации
     *
     * @return ErrorResponseDto
     */
    public function getUnauthorizedResponseData(): ErrorResponseDto
    {
        return ErrorResponseDtoFactory::buildUnauthorized();
    }

    /**
     * Данные для ответа в json: попытка прекратить пользовательский сеанс неавторизованным пользователем
     *
     * @return ErrorResponseDto
     */
    public function getUnauthorizedLogoutResponseData(): ErrorResponseDto
    {
        return ErrorResponseDtoFactory::buildUnauthorizedLogout();
    }
}
