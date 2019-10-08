<?php

namespace App\Services;

use App\Dto\ErrorResponseDto;
use App\Dto\factories\ErrorResponseDtoFactory;
use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\factories\ValidationDtoFactory;
use App\Dto\ResponseDto;
use App\Dto\ResponseDtoInterface;
use App\Dto\ValidationDto;


/**
 * Class BaseService
 * @package App\Services
 *
 * Базовый сервис с основными вариантами ответов
 */
abstract class BaseService
{
    /**
     * Набор правил валидаций
     */
    const VALIDATION_RULES = [];



    /**
     * Ответ "Успешное удаление"
     *
     * @return ResponseDto
     */
    public function deletedResponseData(): ResponseDtoInterface
    {
        return SuccessfulResponseDtoFactory::buildSuccessfulDeleted();
    }

    /**
     * Ответ "Успешное создание"
     *
     * @return ErrorResponseDto
     */
    public function createdResponseDataBase(): ResponseDtoInterface
    {
        return SuccessfulResponseDtoFactory::buildSuccessfulCreated();
    }

    /**
     * Данные для ответа в json: пользователю запрещен доступ к API
     *
     * @return ErrorResponseDto
     */
    public function getAccessDeniedResponseData(): ErrorResponseDto
    {
        return ErrorResponseDtoFactory::buildAccessDenied();
    }
}
