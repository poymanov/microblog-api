<?php

namespace App\Dto;

/**
 * Class LoginResponseDto
 * @package App\Dto
 *
 * Объект для представления ответа успешной аутентификации
 */
class LoginResponseDto implements ResponseDtoInterface
{
    protected $data;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}
