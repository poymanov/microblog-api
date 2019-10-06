<?php

namespace App\Dto\factories;

use App\Dto\ResponseDto;
use App\Dto\ResponseDtoInterface;

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
     * Ответ "Успешное создание объекта"
     *
     * @return ResponseDto
     */
    public static function buildSuccessfulCreated(): ResponseDtoInterface
    {
        $dto = new ResponseDto();
        $dto->setMessage(trans('responses.successfully_created'));

        return $dto;
    }

    /**
     * Ответ "Успешное удаление объекта"
     *
     * @return ResponseDto
     */
    public static function buildSuccessfulDeleted(): ResponseDtoInterface
    {
        $dto = new ResponseDto();
        $dto->setMessage(trans('responses.successfully_deleted'));

        return $dto;
    }
}
