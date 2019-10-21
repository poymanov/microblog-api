<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Получения профиля пользователя
     *
     * @test
     */
    public function get_user_profile()
    {
        $user = factory(User::class)->create();

        $url = route('api.users.show', ['id' => $user->id]);

        $response = $this->getJson($url);
        $response->assertOk();

        $expected = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->timestamp,
            'updated_at' => $user->updated_at->timestamp,
            'subscriptions_count' => $user->subscriptions_count,
            'subscribers_count' => $user->subscribers_count,
        ];

        $response->assertExactJson($expected);
    }

    /**
     * Получения профиля неизвественного пользователя
     *
     * @test
     */
    public function get_unknown_user_profile()
    {
        $url = route('api.users.show', ['id' => 999]);

        $response = $this->getJson($url);
        $response->assertNotFound();
    }

    /**
     * Неавторизованный пользователь не может редактировать профиль
     *
     * @test
     */
    public function unauthorized_can_not_update_profile()
    {
        $url = route('api.users.update');

        $response = $this->patchJson($url);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Попытка обновления профиля с ошибками заполнения
     *
     * @test
     */
    public function update_user_profile_validation_failed()
    {
        $this->authApi();

        $url = route('api.users.update');

        $response = $this->patchJson($url);
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

        $url = route('api.users.update');

        $response = $this->patchJson($url, ['name' => 'Test']);
        $response->assertOk();

        $expected = [
            'id' => $user->id,
            'name' => 'Test',
            'email' => $user->email,
            'created_at' => $user->created_at->timestamp,
            'updated_at' => $user->updated_at->timestamp,
            'subscriptions_count' => $user->subscriptions_count,
            'subscribers_count' => $user->subscribers_count,
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

        $url = route('api.users.update');

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
            'subscriptions_count' => $user->subscriptions_count,
            'subscribers_count' => $user->subscribers_count,
        ];

        $response->assertExactJson($expected);
    }
}
