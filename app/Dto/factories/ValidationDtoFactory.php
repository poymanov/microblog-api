<?php

namespace App\Dto\factories;

use App\Dto\ValidationDto;

/**
 * Class ValidationDtoFactory
 * @package App\Dto\factories
 *
 * Фабрика для создания объектов с состоянием валидации
 */
class ValidationDtoFactory
{
    /**
     * Создание объекта с ошибками валидации
     *
     * @param array $errors
     * @return ValidationDto
     */
    public static function buildFailed(array $errors): ValidationDto
    {
        $dto = new ValidationDto();
        $dto->setErrors($errors);
        $dto->setMessage(trans('responses.validation_failed'));
        $dto->setFailed();

        return $dto;
    }

    /**
     * Создание объекта с успешным статусом
     *
     * @return ValidationDto
     */
    public static function buildOk(): ValidationDto
    {
        $dto = new ValidationDto();
        $dto->setMessage(trans('responses.successfully_created'));
        $dto->setOk();

        return $dto;
    }
}
