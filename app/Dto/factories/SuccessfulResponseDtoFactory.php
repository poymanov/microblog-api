<?php

namespace App\Dto\factories;

use App\Dto\LoginResponseDto;
use App\Dto\ResponseDto;
use App\Dto\ResponseDtoInterface;
use Carbon\Carbon;
use Laravel\Passport\PersonalAccessTokenResult;

/**
 * Class SuccessfulResponseDtoFactory
 * @package App\Dto\factories
 *
 * Фабрика для создания ответов с успешным результатом
 */
class SuccessfulResponseDtoFactory
{
    /**
     * Ответ "Успешная регистрация"
     *
     * @return ResponseDto
     */
    public static function buildSuccessfulSignup(): ResponseDtoInterface
    {
        $dto = new ResponseDto();
        $dto->setMessage(trans('responses.successfully_signup.message'));

        return $dto;
    }

    /**
     * Ответ "Успешное завершение сеанса пользователя"
     *
     * @return ResponseDto
     */
    public static function buildSuccessfulLogout(): ResponseDtoInterface
    {
        $dto = new ResponseDto();
        $dto->setMessage(trans('responses.successfully_logout.message'));

        return $dto;
    }

    /**
     * Ответ "Успешная авторизация"
     *
     * @param PersonalAccessTokenResult $token
     * @return LoginResponseDto
     */
    public static function buildSuccessfulLogin(PersonalAccessTokenResult $token)
    {
        $data = [
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->token->expires_at)->toDateTimeString(),
        ];

        $dto = new LoginResponseDto();
        $dto->setData($data);

        return $dto;
    }
}
