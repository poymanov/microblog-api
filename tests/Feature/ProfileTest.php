<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Неавторизованный пользователь не может получить данные профиля
     *
     * @test
     */
    public function unauthorized_can_not_get_profile_data()
    {
        $url = route('api.profile.show');

        $response = $this->getJson($url);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Неавторизованный пользователь не может редактировать профиль
     *
     * @test
     */
    public function unauthorized_can_not_update_profile()
    {
        $url = route('api.profile.update');

        $response = $this->patchJson($url, []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Пользователь получает данные собственного профиля
     *
     * @test
     */
    public function user_can_get_profile_data()
    {
        $user = $this->authApi();

        $expected = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->timestamp,
            'updated_at' => $user->updated_at->timestamp,
        ];

        $url = route('api.profile.show');

        $response = $this->getJson($url);
        $response->assertOk();

        $response->assertExactJson($expected);
    }

    /**
     * Попытка обновления профиля с ошибками заполнения
     *
     * @test
     */
    public function update_profile_validation_failed()
    {
        $this->authApi();

        $url = route('api.profile.update');

        $response = $this->patchJson($url, []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $errors = [
            'name' => ['The name field is required.'],
        ];

        $expected = $this->buildErrorResponseData(trans('responses.validation_failed'), $errors);

        $response->assertExactJson($expected);
    }

    /**
     * Изменение имени пользователя
     *
     * @test
     */
    public function update_name_successfully()
    {
        $user = $this->authApi();

        $url = route('api.profile.update');

        $response = $this->patchJson($url, ['name' => 'Test']);
        $response->assertOk();

        $expected = [
            'id' => $user->id,
            'name' => 'Test',
            'email' => $user->email,
            'created_at' => $user->created_at->timestamp,
            'updated_at' => $user->updated_at->timestamp,
        ];

        $response->assertExactJson($expected);
    }

    /**
     * Изменение пароля пользователя
     *
     * @test
     */
    public function update_password_successfully()
    {
        $user = $this->authApi();

        $url = route('api.profile.update');

        $response = $this->patchJson($url, [
            'name' => $user->name, 'password' => '123qwe', 'password_confirmation' => '123qwe'
        ]);

        $response->assertOk();

        $expected = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->timestamp,
            'updated_at' => $user->updated_at->timestamp,
        ];

        $response->assertExactJson($expected);
    }
}
