<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ResponseDto
 * @package App\Dto
 *
 * Объект для представления стандартных ответов
 */
class ResponseDto implements Arrayable
{
    /** @var string Описание ответа */
    protected $message;

    /**
     * Представление ответа в виде массива
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => [
                'message' => $this->message,
            ]
        ];
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
