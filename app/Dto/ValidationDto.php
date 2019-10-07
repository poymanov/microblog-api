<?php

namespace App\Dto;

/**
 * Class ValidationDto
 * @package App\Dto
 *
 * Объект для передачи статуса валидации
 */
class ValidationDto extends ResponseDto
{
    /** Состояние валидации - успешное */
    private const STATUS_OK = 1;

    /** Состояние валидации - были ошибки */
    private const STATUS_FAILED = 2;

    /** @var array Список ошибок */
    public $errors = [];

    /** @var int Состояние валидации объекта */
    private $status;

    /**
     * Проверка: валидация прошла без ошибок
     *
     * @return bool
     */
    public function isOk()
    {
        return $this->status == self::STATUS_OK;
    }

    /**
     * Проверка: при валидации возникли ошибки
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status == self::STATUS_FAILED;
    }

    /**
     * Статус валидации - успешный
     */
    public function setOk(): void
    {
        $this->status = self::STATUS_OK;
    }

    /**
     * Статус валидации - были ошибки
     */
    public function setFailed(): void
    {
        $this->status = self::STATUS_FAILED;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['data']['errors'] = $this->errors;

        return $data;
    }
}
