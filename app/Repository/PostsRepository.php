<?php

namespace App\Repository;

use App\Post;

/**
 * Class PostsRepository
 * @package App\Repository
 *
 * Репозиторий для управления публикациями
 */
class PostsRepository
{
    /**
     * Получение публикаций пользователя
     *
     * @param int $userId
     * @param int $perPage
     * @return mixed
     */
    public function getByUserId(int $userId, int $perPage)
    {
        return Post::where(['user_id' => $userId])->latest()->paginate($perPage);
    }

    /**
     * Создание публикации
     *
     * @param $data
     */
    public function create($data)
    {
        Post::create($data);
    }
}
