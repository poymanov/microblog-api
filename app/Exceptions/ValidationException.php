<?php

namespace App\Exceptions;

use Exception;

/**
 * Class ValidationException
 * @package App\Exceptions
 *
 * Исключение возникающее при ошибках валидации
 * и содержащее эти ошибки
 */
class ValidationException extends Exception
{
    /** @var array Ошибки валидации */
    private $errors;

    /**
     * ValidationException constructor.
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
