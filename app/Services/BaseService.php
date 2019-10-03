<?php

namespace App\Services;

use App\Dto\ErrorResponseDto;
use App\Dto\factories\ErrorResponseDtoFactory;
use App\Dto\factories\ValidationDtoFactory;
use App\Dto\ValidationDto;
use Illuminate\Http\Request;
use Validator;

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
     * Валидация запроса и возврат ответа в json
     * 
     * @param Request $request
     * @return ValidationDto
     */
    public function validateJsonRequest(Request $request): ValidationDto
    {
        $validationData = $request->all();
        $validationData['user_id'] = request()->user()->id;

        $validator = Validator::make($validationData, static::VALIDATION_RULES);

        if ($validator->fails()) {
            return ValidationDtoFactory::buildFailed($validator->errors()->toArray());
        }

        return ValidationDtoFactory::buildOk($validator->getData());
    }

    /**
     * Ответ "Успешное удаление"
     *
     * @return array
     */
    public function deletedResponseData()
    {
        return $this->createJsonResponseData(trans('responses.successfully_deleted'));
    }

    /**
     * Ответ "Успешное создание"
     *
     * @return array
     */
    public function createdResponseDataBase()
    {
        return $this->createJsonResponseData(trans('responses.successfully_created'));
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

    /**
     * Заготовка для ответа
     *
     * @param $message
     * @return array
     */
    protected function createJsonResponseData($message)
    {
        return [
            'data' => [
                'message' => $message,
            ]
        ];
    }


    /**
     * Базовая структура данных ответа для json
     *
     * @param $message
     * @param $errors
     * @return array
     */
    protected function getErrorResponseScheme($message, $errors)
    {
        return [
            'data' => [
                'message' => $message,
                'errors' => $errors,
            ],
        ];
    }
}
