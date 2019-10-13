<?php

namespace Tests\Unit\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Post;
use App\Services\PostService;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostsServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var PostService Тестируемый сервис */
    private $service;

    /**
     * PostsServiceTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(PostService::class);
    }

    /**
     * Получение публикации по id
     *
     * @test
     */
    public function get_post_by_id()
    {
        $expected = factory(Post::class)->create();
        $actual = $this->service->getById($expected->id);

        $this->assertEquals($expected->id, $actual->id);
    }

    /**
     * Получение неизвестной публикации
     *
     * @test
     */
    public function get_not_existed_post()
    {
        $actual = $this->service->getById(999);

        $this->assertNull($actual);
    }

    /**
     * Получение публикаций для несуществующего пользователя
     *
     * @test
     * @throws NotFoundException
     */
    public function get_not_existed_user_posts()
    {
        $this->expectException(NotFoundException::class);
        $this->service->getUserPosts(999);
    }

    /**
     * Получение публикаций пользователя
     *
     * @test
     * @throws NotFoundException
     */
    public function get_user_posts()
    {
        $user = factory(User::class)->create();

        $firstPost = factory(Post::class)->create(['user_id' => $user->id, 'created_at' => Carbon::create(2019, 1, 2)]);
        $secondPost = factory(Post::class)->create(['user_id' => $user->id, 'created_at' => Carbon::create(2019, 1, 3)]);

        $actualPosts = $this->service->getUserPosts($user->id);

        $this->assertEquals($secondPost->id, $actualPosts->first()->id);
        $this->assertEquals($firstPost->id, $actualPosts->last()->id);
    }

    /**
     * Ошибка валидации при создании публикации
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function create_post_validation_failed()
    {
        $this->expectException(ValidationException::class);

        $post = factory(Post::class)->make(['text' => null]);
        $this->service->createPost($post->toArray(), $post->user_id);
    }

    /**
     * Успешное создание публикации
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function create_post()
    {
        $post = factory(Post::class)->make();

        $expected = SuccessfulResponseDtoFactory::buildSuccessfulCreated();
        $actual = $this->service->createPost($post->toArray(), $post->user_id);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Попытка создания публикации для несуществующего пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function create_post_with_not_existed_user()
    {
        $this->expectException(NotFoundException::class);

        $post = factory(Post::class)->make();
        $this->service->createPost($post->toArray(), 999);
    }

    /**
     * Удаление публикации для несуществующего пользователя
     *
     * @test
     * @throws AccessDeniedException
     * @throws NotFoundException
     */
    public function delete_post_for_not_existed_user()
    {
        $this->expectException(NotFoundException::class);
        $this->service->deletePost(999, 999);
    }

    /**
     * Попытка удаления несуществующей публикации
     *
     * @test
     * @throws AccessDeniedException
     * @throws NotFoundException
     */
    public function delete_not_existed_post()
    {
        $user = factory(User::class)->create();

        $this->expectException(NotFoundException::class);
        $this->service->deletePost(999, $user->id);
    }

    /**
     * Попытка удаления чужой публикации
     *
     * @test
     * @throws AccessDeniedException
     * @throws NotFoundException
     */
    public function delete_another_user_post()
    {
        $this->expectException(AccessDeniedException::class);

        $user = factory(User::class)->create();

        $anotherUserPost = factory(Post::class)->create();

        $this->service->deletePost($anotherUserPost->id, $user->id);
    }

    /**
     * Удаление публикации
     *
     * @test
     * @throws AccessDeniedException
     * @throws NotFoundException
     */
    public function delete_post()
    {
        $expected = SuccessfulResponseDtoFactory::buildSuccessfulDeleted();

        $post = factory(Post::class)->create();

        $actual = $this->service->deletePost($post->id, $post->user_id);

        $this->assertEquals($expected, $actual);

        $this->assertDatabaseMissing('posts', ['id' => $post->id, 'user_id' => $post->user_id]);
    }
}
