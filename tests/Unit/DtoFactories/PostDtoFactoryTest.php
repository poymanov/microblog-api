<?php

namespace Tests\Unit\DtoFactories;

use App\Dto\factories\PostDtoFactory;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostDtoFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Создание публикации
     *
     * @test
     */
    public function build_post_dto()
    {
        $post = factory(Post::class)->create();

        $actual = PostDtoFactory::buildPost($post);

        $this->assertEquals($post->id, $actual->getId());
        $this->assertEquals($post->text, $actual->getText());
        $this->assertEquals($post->user_id, $actual->getUserId());
        $this->assertEquals($post->created_at, $actual->getCreatedAt());
        $this->assertEquals($post->updated_at, $actual->getUpdatedAt());
    }
}
