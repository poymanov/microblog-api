<?php

namespace Tests\Unit\Services;

use App\Dto\models\PostDto;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Post;
use App\Services\PostService;
use App\User;
use App\UserSubscribe;
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
     * @throws NotFoundException
     */
    public function get_post_by_id()
    {
        $expected = factory(Post::class)->create();
        $actual = $this->service->getById($expected->id);

        $this->assertInstanceOf(PostDto::class, $actual);

        $this->assertEquals($expected->id, $actual->getId());
    }

    /**
     * Получение неизвестной публикации
     *
     * @test
     * @throws NotFoundException
     */
    public function get_not_existed_post()
    {
        $this->expectException(NotFoundException::class);
        $this->service->getById(999);
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

        $this->assertIsArray($actualPosts);

        $this->assertEquals($secondPost->id, $actualPosts[0]->getId());
        $this->assertEquals($firstPost->id, $actualPosts[1]->getId());
    }

    /**
     * Получение публикаций пользователя в виде распакованного массива
     *
     * @test
     * @throws NotFoundException
     */
    public function get_user_post_as_extracted_array()
    {
        $user = factory(User::class)->create();

        $firstPost = factory(Post::class)->create(['user_id' => $user->id, 'created_at' => Carbon::create(2019, 1, 2)]);
        $secondPost = factory(Post::class)->create(['user_id' => $user->id, 'created_at' => Carbon::create(2019, 1, 3)]);

        $actualPosts = $this->service->getUserPostsExtracted($user->id);

        $this->assertIsArray($actualPosts);

        $this->assertEquals($secondPost->id, $actualPosts[0]['id']);
        $this->assertEquals($firstPost->id, $actualPosts[1]['id']);
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

        $actual = $this->service->createPost($post->toArray(), $post->user_id);

        $this->assertEquals($post->text, $actual->getText());
        $this->assertEquals($post->user_id, $actual->getUserId());
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
        $post = factory(Post::class)->create();

        $this->service->deletePost($post->id, $post->user_id);

        $this->assertDatabaseMissing('posts', ['id' => $post->id, 'user_id' => $post->user_id]);
    }

    /**
     * Получение пустой ленты пользователя
     *
     * @test
     * @throws NotFoundException
     */
    public function get_empty_feed()
    {
        $user = factory(User::class)->create();

        $actual = $this->service->userFeed($user->id);

        $this->assertEquals([], $actual);
    }

    /**
     * Получение ленты пользователя
     *
     * @test
     * @throws NotFoundException
     */
    public function get_user_feed()
    {
        $subscriber = factory(User::class)->create();

        $publisher1 = factory(User::class)->create();
        $publisher2 = factory(User::class)->create();

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber->id, 'publisher_id' => $publisher1->id
        ]);

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber->id, 'publisher_id' => $publisher2->id
        ]);

        $post1 = factory(Post::class)->create(['user_id' => $subscriber->id]);
        $post2 = factory(Post::class)->create(['user_id' => $publisher1->id]);
        $post3 = factory(Post::class)->create(['user_id' => $publisher2->id]);

        $post1Dto = new PostDto($post1->id, $post1->text, $post1->user_id, $post1->created_at, $post1->updated_at);
        $post2Dto = new PostDto($post2->id, $post2->text, $post2->user_id, $post2->created_at, $post2->updated_at);
        $post3Dto = new PostDto($post3->id, $post3->text, $post3->user_id, $post3->created_at, $post3->updated_at);

        $actual = $this->service->userFeed($subscriber->id);

        $expected = [$post1Dto, $post2Dto, $post3Dto];

        $this->assertEquals($expected, $actual);
    }
}
