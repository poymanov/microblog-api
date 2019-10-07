<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

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

        $errors = [
            'text' => ['The text field is required.'],
        ];

        $expected = $this->buildErrorResponseData(trans('responses.validation_failed'), $errors);

        $response->assertExactJson($expected);
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

        $expected = $this->buildResponseData(trans('responses.successfully_created'));

        $response->assertExactJson($expected);
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
    public function delete_post()
    {
        $user = $this->authApi();

        $post = factory(Post::class)->create(['user_id' => $user->id]);
        $url = route('api.posts.destroy', $post);

        $response = $this->json('delete', $url);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);

        $expected = $this->buildResponseData(trans('responses.successfully_deleted'));

        $response->assertExactJson($expected);
    }

    /**
     * Список публикаций пользователя
     *
     * @test
     */
    public function get_user_posts()
    {
        $user = factory(User::class)->create();
        $posts = factory(Post::class, 5)->create(['user_id' => $user->id]);
        $postsArray = $this->postsToArray($posts);

        $url = route('api.posts.user', $user);

        $response = $this->json('get', $url);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            'data' => $postsArray,
            'links' => [
                'first' => $url.'?page=1',
                'last' => $url.'?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'path' => $url,
                'per_page' => 10,
                'to' => 5,
                'total' => 5
            ]
        ]);
    }

    /**
     * Список публикаций пользователя с пагинацией
     *
     * @test
     */
    public function get_user_posts_with_pagination()
    {
        $user = factory(User::class)->create();
        factory(Post::class, 20)->create(['user_id' => $user->id]);

        $url = route('api.posts.user', $user);

        $response = $this->json('get', $url);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['links' => [
            'first' => $url.'?page=1',
            'last' => $url.'?page=2',
            'next' => $url.'?page=2',
            'prev' => null,
        ]]);
    }

    /**
     * Список публикация в виде массива
     * @param $posts
     * @return array|mixed
     */
    protected function postsToArray($posts)
    {
        $postsArray = [];

        foreach ($posts as $post) {
            $postsArray[] = [
                'id' => $post->id,
                'text' => $post->text,
                'created_at' => $post->created_at->timestamp,
            ];
        }
        return count($postsArray) == 1 ? $postsArray[0] : $postsArray;
    }
}
