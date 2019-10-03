<?php

namespace App\Dto\factories;

use App\Dto\ResponseDto;

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
    public static function buildSuccessfulSignup(): ResponseDto
    {
        $dto = new ResponseDto();
        $dto->setMessage(trans('responses.successfully_signup.message'));

        return $dto;
    }
}
