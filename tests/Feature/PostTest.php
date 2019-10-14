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

        $response->assertJsonFragment([
            'text' => $post->text,
            'user_id' => $user->id,
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
     * Удаление несуществующей публикации
     *
     * @test
     */
    public function delete_not_existed_post()
    {
        $this->authApi();

        $url = route('api.posts.destroy', 999);

        $response = $this->json('delete', $url);

        $expected = $this->buildErrorResponseData(trans('responses.not_found'));

        $response->assertExactJson($expected);
    }

    /**
     * Удаление публикации
     *
     * @test
     */
    public function delete_post()
    {
        $this->withoutExceptionHandling();
        $user = $this->authApi();

        $post = factory(Post::class)->create(['user_id' => $user->id]);
        $url = route('api.posts.destroy', $post);

        $response = $this->json('delete', $url);
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    /**
     * Получение публикаций несуществующего пользователя
     *
     * @test
     */
    public function get_not_existed_user_post()
    {
        $expected = $this->buildErrorResponseData(trans('responses.not_found'));

        $url = route('api.posts.user', 999);
        $response = $this->json('get', $url);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
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
        $response->assertExactJson($postsArray);
    }

    /**
     * Список публикаций пользователя с пагинацией
     *
     * @test
     */
    public function get_user_posts_with_pagination()
    {
        $user = factory(User::class)->create();
        $posts = factory(Post::class, 20)->create(['user_id' => $user->id]);
        $postsArray = $this->postsToArray($posts->take(10));

        $url = route('api.posts.user', $user);

        $response = $this->json('get', $url);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson($postsArray);
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
                'user_id' => $post->user_id,
                'created_at' => $post->created_at->timestamp,
                'updated_at' => $post->updated_at->timestamp,
            ];
        }
        return count($postsArray) == 1 ? $postsArray[0] : $postsArray;
    }
}
