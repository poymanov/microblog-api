<?php

namespace App\Dto;

/**
 * Class ErrorResponseDto
 * @package App\Dto
 *
 * Объект для представления ответов cо списком ошибок
 */
class ErrorResponseDto extends ResponseDto
{
    /** @var array Описание ошибок */
    protected $errors = [];

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
}
