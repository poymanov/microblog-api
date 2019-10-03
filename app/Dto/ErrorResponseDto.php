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
    /** @var string Описание ошибок */
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
     * @return string
     */
    public function getErrors(): string
    {
        return $this->errors;
    }

    /**
     * @param string $errors
     */
    public function setErrors(string $errors): void
    {
        $this->errors = $errors;
    }
}
