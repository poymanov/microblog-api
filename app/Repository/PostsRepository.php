<?php

namespace App\Repository;

use App\Post;

/**
 * Class PostsRepository
 * @package App\Repository
 *
 * Репозиторий для управления публикациями
 */
class PostsRepository extends AbstractRepository
{
    /**
     * Получение публикации по id
     *
     * @param int $id
     * @return Post|null
     */
    public function getById(int $id): ?Post
    {
        return Post::find($id);
    }

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
    public function create(array $data): void
    {
        Post::create($data);
    }

    /**
     * Удаление публикации
     *
     * @param int $id
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $this->getById($id)->delete();
    }
}
