<?php

namespace App\Dto\factories;

use App\Dto\ErrorResponseDto;

/**
 * Class ErrorResponseDtoFactory
 * @package App\Dto\factories
 *
 * Фабрика для создания ответов с ошибками
 */
class ErrorResponseDtoFactory
{
    /**
     * Ответ с ошибкой "Доступ запрещен"
     *
     * @return ErrorResponseDto
     */
    public static function buildAccessDenied(): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.access_denied.message'));
        $dto->setErrors([trans('responses.access_denied.errors')]);

        return $dto;
    }

    /**
     * Ответ с ошибкой "Неавторизованный доступ"
     *
     * @return ErrorResponseDto
     */
    public static function buildUnauthorized(): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.unauthorized.message'));
        $dto->setErrors([trans('responses.unauthorized.errors')]);

        return $dto;
    }

    /**
     * Ответ с ошибкой "Попытка завершения сеанса неавторизованным пользователем"
     *
     * @return ErrorResponseDto
     */
    public static function buildUnauthorizedLogout(): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.unauthorized_logout.message'));
        $dto->setErrors([trans('responses.unauthorized_logout.errors')]);

        return $dto;
    }

    /**
     * Ответ с ошибкой "Ошибки валидации"
     *
     * @param array $errors
     * @return ErrorResponseDto
     */
    public static function buildValidationFailed(array $errors): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.validation_failed'));
        $dto->setErrors($errors);

        return $dto;
    }

    /**
     * Ответ с ошибкой "Объект не найден"
     *
     * @return ErrorResponseDto
     */
    public static function buildNotFound(): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.not_found'));

        return $dto;
    }

    /**
     * Ответ с ошибкой "Ошибка без категории"
     *
     * @param array $errors
     * @return ErrorResponseDto
     */
    public static function buildSomethingWentWrong(array $errors): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.something_went_wrong'));
        $dto->setErrors($errors);

        return $dto;
    }

    /**
     * Ответ с ошибкой "Попытка подписаться на самого себя"
     *
     * @return ErrorResponseDto
     */
    public static function buildSubscribeHimself(): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.user_subscribe_himself'));

        return $dto;
    }

    /**
     * Ответ с ошибкой "Попытка отписаться от пользователя, на которого не подписан"
     *
     * @return ErrorResponseDto
     */
    public static function buildUnsubscribeFromNotSubscribed(): ErrorResponseDto
    {
        $dto = new ErrorResponseDto();
        $dto->setMessage(trans('responses.user_unsubscribe_from_not_subscribed'));

        return $dto;
    }
}
