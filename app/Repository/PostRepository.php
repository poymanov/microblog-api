<?php

namespace App\Repository;

use App\Dto\factories\PostDtoFactory;
use App\Dto\models\PostDto;
use App\Exceptions\NotFoundException;
use App\Post;

/**
 * Class PostRepository
 * @package App\Repository
 *
 * Репозиторий для управления публикациями
 */
class PostRepository extends AbstractRepository
{
    /**
     * Получение публикации по id
     *
     * @param int $id
     * @return PostDto
     * @throws NotFoundException
     */
    public function getById(int $id): PostDto
    {
        $post = Post::find($id);

        if (is_null($post)) {
            throw new NotFoundException();
        }

        return PostDtoFactory::buildPost($post);
    }

    /**
     * Получение публикаций пользователя
     *
     * @param int $userId
     * @param int $perPage
     * @return PostDto[]
     */
    public function getByUserId(int $userId, int $perPage): array
    {
        $dtos = [];

        $posts = Post::where(['user_id' => $userId])->latest()->paginate($perPage);

        foreach ($posts as $post) {
            $dtos[] = PostDtoFactory::buildPost($post);
        }

        return $dtos;
    }

    /**
     * Создание публикации
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        return Post::create($data)->id;
    }

    /**
     * Удаление публикации
     *
     * @param int $id
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        Post::find($id)->delete();
    }

    /**
     * Правила валидации для создания публикации
     *
     * @return array
     */
    public function getCreatingValidationRules(): array
    {
        return [
            'text' => 'required|max:300',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
