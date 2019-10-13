<?php

namespace App\Services;

use App\Exceptions\NotFoundException;

/**
 * Class BaseService
 * @package App\Services
 *
 * Базовый сервис с основными вариантами ответов
 */
abstract class BaseService
{
    /**
     * Создание исключения, если объект - null
     *
     * @param $object
     * @param string $exceptionClass
     */
    public function throwExceptionIfNull($object, string $exceptionClass = NotFoundException::class): void
    {
        if (is_null($object)) {
            throw new $exceptionClass;
        }
    }
}
