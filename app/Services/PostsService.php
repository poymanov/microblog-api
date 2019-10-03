<?php

namespace App\Services;

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
     * @return array
     */
    public function createdResponseData()
    {
        return $this->createdResponseDataBase();
    }
}
