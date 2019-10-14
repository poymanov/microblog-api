<?php

namespace Tests\Unit\Dto;

use App\Dto\models\PostDto;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostDtoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Создание DTO из объекта публикации
     *
     * @test
     */
    public function creating_post_dto_from_entity()
    {
        $post = factory(Post::class)->create();

        $dto = new PostDto($post->id, $post->text, $post->user_id, $post->created_at, $post->updated_at);

        $this->assertEquals($post->id, $dto->getId());
        $this->assertEquals($post->text, $dto->getText());
        $this->assertEquals($post->user_id, $dto->getUserId());
        $this->assertEquals($post->created_at, $dto->getCreatedAt());
        $this->assertEquals($post->updated_at, $dto->getUpdatedAt());
    }

    /**
     * Представление DTO публикации в виде массива
     *
     * @test
     */
    public function get_post_dto_as_array()
    {
        $post = factory(Post::class)->create();

        $expected = [
            'id' => $post->id,
            'text' => $post->text,
            'user_id' => $post->user_id,
            'created_at' => $post->created_at->timestamp,
            'updated_at' => $post->created_at->timestamp,
        ];

        $dto = new PostDto($post->id, $post->text, $post->user_id, $post->created_at, $post->updated_at);

        $this->assertIsArray($dto->toArray());
        $this->assertEquals($expected, $dto->toArray());
    }
}
