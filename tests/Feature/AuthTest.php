<?php

namespace Tests\Feature;

use App\Services\AuthService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ошибки валидации при попытке регистрации
     *
     * @test
     */
    public function signup_validation_failed()
    {
        $response = $this->json('post', route('api.auth.signup'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertExactJson([
            'data' => [
                'message' => AuthService::RESPONSE_DATA['VALIDATION_FAILED']['MESSAGE'],
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]
        ]);
    }

    /**
     * Успешная регистрация
     *
     * @test
     */
    public function user_can_signup()
    {
        $user = factory(User::class)->make();

        $response = $this->json('post', route('api.auth.signup'),
            array_merge($user->toArray(), ['password' => '123qwe', 'password_confirmation' => '123qwe']));

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertExactJson([
            'data' => [
                'message' => AuthService::RESPONSE_DATA['SUCCESSFULLY_SIGNUP']['MESSAGE'],
            ]
        ]);

        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email]);
    }

    /**
     * Попытка авторизации без указания данных пользователя
     *
     * @test
     */
    public function login_validation_failed()
    {
        $response = $this->json('post', route('api.auth.login'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertExactJson([
            'data' => [
                'message' => AuthService::RESPONSE_DATA['VALIDATION_FAILED']['MESSAGE'],
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]
        ]);
    }
    /**
     * Попытка авторизации данными пользователя, которого нет в базе
     *
     * @test
     */
    public function login_unknown_user()
    {
        $authData = ['email' => 'test@test.ru', 'password' => '123qwe'];
        $response = $this->json('post', route('api.auth.login'), $authData);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertExactJson([
            'data' => [
                'message' => AuthService::RESPONSE_DATA['UNAUTHORIZED']['MESSAGE'],
                'errors' => AuthService::RESPONSE_DATA['UNAUTHORIZED']['ERRORS'],
            ]
        ]);
    }

    /**
     * Попытка прекратить сеанс авторизации неавторизованным пользователем
     *
     * @test
     */
    public function logout_as_unauthorized_user()
    {
        $response = $this->json('get', route('api.auth.logout'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertExactJson([
            'data' => [
                'message' => AuthService::RESPONSE_DATA['UNAUTHORIZED_LOGOUT']['MESSAGE'],
                'errors' => AuthService::RESPONSE_DATA['UNAUTHORIZED_LOGOUT']['ERRORS'],
            ]
        ]);
    }

    /**
     * Успешная авторизация
     *
     * @test
     */
    public function login_successful()
    {
        $this->createOauthClient();

        $user = factory(User::class)->create();
        $authData = ['email' => $user->email, 'password' => 'secret'];

        $response = $this->json('post', route('api.auth.login'), $authData);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Прекращение сеанса авторизованного пользователя
     *
     * @test
     */
    public function authorized_user_can_logout()
    {
        $this->authApi();

        $response = $this->json('get', route('api.auth.logout'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            'data' => [
                'message' => AuthService::RESPONSE_DATA['SUCCESSFULLY_LOGOUT']['MESSAGE'],
            ]
        ]);
    }
}
