<?php

namespace App\Dto;

/**
 * Class ValidationDto
 * @package App\Dto
 *
 * Объект для передачи статуса валидации
 */
class ValidationDto extends ErrorResponseDto
{
    /** Состояние валидации - успешное */
    private const STATUS_OK = 1;

    /** Состояние валидации - были ошибки */
    private const STATUS_FAILED = 2;

    /** @var array Успешно провалидированные данные */
    protected $data = [];

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
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
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
}
