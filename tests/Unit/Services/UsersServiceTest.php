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
        $expected = SuccessfulResponseDtoFactory::buildSuccessfulSignup();

        $user = factory(User::class)->make();
        $actual = $this->service->registerUser(
            array_merge(
                $user->toArray(),
                ['password' => '123qwe', 'password_confirmation' => '123qwe']
            )
        );

        $this->assertEquals($expected, $actual);

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
}
