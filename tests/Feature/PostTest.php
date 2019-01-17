<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Неавторизованный пользователь пытается создать публикацию
     *
     * @test
     */
    public function unauthorized_user_can_not_create_post()
    {
        $url = route('api.posts.store');

        $response = $this->json('post', $url, []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Попытка создать публикацию без указания данных
     *
     * @test
     */
    public function create_post_validation_failed()
    {
        $url = route('api.posts.store');

        $this->authApi();

        $response = $this->json('post', $url, []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertExactJson([
            'data' => [
                'message' => 'Validation failed',
                'errors' => [
                    'text' => ['The text field is required.'],
                ],
            ]
        ]);
    }

    /**
     * Успешное создание новой публикации
     *
     * @test
     */
    public function create_post_success()
    {
        $user = $this->authApi();

        $post = factory(Post::class)->make(['user_id' => null]);

        $response = $this->json('post', route('api.posts.store'), $post->toArray());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('posts', [
            'text' => $post->text,
            'user_id' => $user->id,
        ]);

        $response->assertExactJson([
            'data' => [
                'message' => 'Successfully created',
            ]
        ]);
    }

    /**
     * Неавторизованный пользователь пытается удалить публикацию
     *
     * @test
     */
    public function unauthorized_user_can_not_delete_post()
    {
        $post = factory(Post::class)->create();
        $url = route('api.posts.destroy', $post);

        $response = $this->json('delete', $url);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Пользователь не может удалить публикацию другого пользователя
     *
     * @test
     */
    public function delete_another_user_post()
    {
        $this->authApi();

        $post = factory(Post::class)->create();
        $url = route('api.posts.destroy', $post);

        $response = $this->json('delete', $url);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Удаление публикации
     *
     * @test
     */
    public function delete_book()
    {
        $user = $this->authApi();

        $post = factory(Post::class)->create(['user_id' => $user->id]);
        $url = route('api.posts.destroy', $post);

        $response = $this->json('delete', $url);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);

        $response->assertExactJson([
            'data' => [
                'message' => 'Successfully deleted',
            ]
        ]);
    }
}
