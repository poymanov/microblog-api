<?php

namespace Tests\Unit\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\LoginResponseDto;
use App\Dto\models\UserDto;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationException;
use App\Services\UserService;
use App\User;
use App\UserSubscribe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var UserService Тестируемый сервис */
    private $service;

    /**
     * PostsServiceTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(UserService::class);
    }

    /**
     * Получение пользователя по id
     *
     * @test
     * @throws NotFoundException
     */
    public function get_user_by_id()
    {
        $expected = factory(User::class)->create();
        $actual = $this->service->getById($expected->id);

        $this->assertInstanceOf(UserDto::class, $actual);

        $this->assertEquals($expected->id, $actual->getId());
    }

    /**
     * Получение неизвестного пользователя
     *
     * @test
     * @throws NotFoundException
     */
    public function get_not_existed_user()
    {
        $this->expectException(NotFoundException::class);
        $this->service->getById(999);
    }

    /**
     * Ошибки валидации при регистрации пользователя
     *
     * @test
     * @throws ValidationException
     */
    public function register_user_validation_failed()
    {
        $this->expectException(ValidationException::class);

        $user = factory(User::class)->make(['password' => null]);

        $this->service->registerUser($user->toArray());
    }

    /**
     * Успешная регистрация пользователя
     *
     * @test
     * @throws ValidationException
     */
    public function register_user()
    {
        $user = factory(User::class)->make();
        $this->service->registerUser(
            array_merge(
                $user->toArray(),
                ['password' => '123qwe', 'password_confirmation' => '123qwe']
            )
        );

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Ошибки валидации при аутентификации пользователя
     *
     * @test
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function login_user_validation_failed()
    {
        $this->expectException(ValidationException::class);

        $user = factory(User::class)->make(['password' => null]);

        $this->service->loginUser($user->toArray());
    }

    /**
     * Попытка аутентификации с использованием несуществующих данных
     *
     * @test
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function login_not_existed_user()
    {
        $this->expectException(UnauthorizedException::class);

        $authData = ['email' => 'test@test.ru', 'password' => '123qwe'];

        $this->service->loginUser($authData);
    }

    /**
     * Успешная аутентификация пользователя
     *
     * @test
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function login_successful()
    {
        $this->createOauthClient();

        $user = factory(User::class)->create();
        $authData = ['email' => $user->email, 'password' => 'secret'];

        $actual = $this->service->loginUser($authData);

        $this->assertInstanceOf(LoginResponseDto::class, $actual);
    }

    /**
     * Попытка завершения сеанса неавторизованным пользователем
     *
     * @test
     */
    public function user_logout()
    {
        $this->authApi();

        $expected = SuccessfulResponseDtoFactory::buildSuccessfulLogout();

        $actual = $this->service->logoutUser();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Попытка редактирования несуществующего пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update_unknown_user()
    {
        $this->expectException(NotFoundException::class);

        $this->service->updateUser(['name' => 'test'], 999);
    }

    /**
     * Ошибки валидации при редактировании пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update_user_validation_failed()
    {
        $user = factory(User::class)->create();

        $this->expectException(ValidationException::class);

        $this->service->updateUser([], $user->id);
    }

    /**
     * Редактирование имени пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update_user_name_successfully()
    {
        $user = factory(User::class)->create();

        $actual = $this->service->updateUser(['name' => 'Test'], $user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Test',
        ]);

        $this->assertInstanceOf(UserDto::class, $actual);
        $this->assertEquals($user->id, $actual->getId());
    }

    /**
     * Ошибки валидации при изменении пароля пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update_user_password_validation_failed()
    {
        $user = factory(User::class)->create();

        $this->expectException(ValidationException::class);

        $this->service->updateUser(['name' => 'test', 'password' => '123qwe'], $user->id);
    }

    /**
     * Ошибки валидации при изменении пароля пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update_user_password_successfully()
    {
        $user = factory(User::class)->create();

        $actual = $this->service->updateUser(
            ['name' => 'test', 'password' => '123qwe', 'password_confirmation' => '123qwe'],
            $user->id
        );

        $this->assertInstanceOf(UserDto::class, $actual);
        $this->assertEquals($user->id, $actual->getId());
    }


    /**
     * Получение подписок для несуществующего пользователя
     *
     * @test
     */
    public function get_not_existed_user_subscriptions()
    {
        $this->expectException(NotFoundException::class);
        $this->service->getSubscriptions(999);
    }

    /**
     * Получение пустого списка подписок
     *
     * @test
     */
    public function get_empty_subscriptions()
    {
        $subscriber = factory(User::class)->create();

        $actual = $this->service->getSubscriptions($subscriber->id);

        $this->assertEquals([], $actual);
    }

    /**
     * Получение подписок
     *
     * @test
     */
    public function get_subscriptions()
    {
        $subscriber = factory(User::class)->create();

        $publisher1 = factory(User::class)->create();
        $publisher2 = factory(User::class)->create();

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber->id,
            'publisher_id' => $publisher1->id,
        ]);

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber->id,
            'publisher_id' => $publisher2->id,
        ]);

        $expected[] = new UserDto(
            $publisher1->id, $publisher1->name,
            $publisher1->email, $publisher1->created_at,
            $publisher1->updated_at, $publisher1->subscriptions_count, $publisher1->subscribers_count
        );

        $expected[] = new UserDto(
            $publisher2->id, $publisher2->name,
            $publisher2->email, $publisher2->created_at,
            $publisher2->updated_at, $publisher2->subscriptions_count, $publisher2->subscribers_count
        );

        $actual = $this->service->getSubscriptions($subscriber->id);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Получение подписчиков для несуществующего пользователя
     *
     * @test
     */
    public function get_not_existed_user_subscribers()
    {
        $this->expectException(NotFoundException::class);
        $this->service->getSubscribers(999);
    }

    /**
     * Получение пустого списка подписчиков
     *
     * @test
     */
    public function get_empty_subscribers()
    {
        $publisher = factory(User::class)->create();

        $actual = $this->service->getSubscribers($publisher->id);

        $this->assertEquals([], $actual);
    }

    /**
     * Получение списка подписчиков
     *
     * @test
     */
    public function get_subscribers()
    {
        $publisher = factory(User::class)->create();

        $subscriber1 = factory(User::class)->create();
        $subscriber2 = factory(User::class)->create();

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber1->id,
            'publisher_id' => $publisher->id,
        ]);

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber2->id,
            'publisher_id' => $publisher->id,
        ]);

        $expected[] = new UserDto(
            $subscriber1->id, $subscriber1->name,
            $subscriber1->email, $subscriber1->created_at,
            $subscriber1->updated_at, $subscriber1->subscriptions_count, $subscriber1->subscribers_count
        );

        $expected[] = new UserDto(
            $subscriber2->id, $subscriber2->name,
            $subscriber2->email, $subscriber2->created_at,
            $subscriber2->updated_at, $subscriber2->subscriptions_count, $subscriber2->subscribers_count
        );

        $actual = $this->service->getSubscribers($publisher->id);

        $this->assertEquals($expected, $actual);
    }
}
