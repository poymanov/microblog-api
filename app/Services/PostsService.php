<?php

namespace App\Services;

use App\Dto\ResponseDtoInterface;

/**
 * Class PostsService
 * @package App\Services
 *
 * Сервис управления публикациями
 */
class PostsService extends BaseService
{
    const VALIDATION_RULES = [
        'text' => 'required|max:300',
        'user_id' => 'required|exists:users,id',
    ];

    /**
     * Ответ "Успешное создание"
     *
     * @return ResponseDtoInterface
     */
    public function createdResponseData(): ResponseDtoInterface
    {
        return $this->createdResponseDataBase();
    }
}
