<?php

namespace App\Dto\factories;

use App\Dto\models\PostDto;
use App\Post;

/**
 * Class PostDtoFactory
 * @package App\Dto\factories
 *
 * Фабрика для создания DTO объектов публикаций
 */
class PostDtoFactory
{
    /**
     * Создание DTO публикации
     *
     * @param Post $post
     * @return PostDto
     */
    public static function buildPost(Post $post)
    {
        return new PostDto(
            $post->id,
            $post->text,
            $post->user_id,
            $post->created_at,
            $post->updated_at
        );
    }
}
