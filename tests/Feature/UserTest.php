<?php

namespace Tests\Feature;

use App\Dto\models\UserDto;
use App\User;
use App\UserSubscribe;
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

    /**
     * Получение подписок несуществующего пользователя
     *
     * @test
     */
    public function get_not_existed_user_subscriptions()
    {
        $url = route('api.users.subscriptions', ['id' => 999]);

        $expected = $this->buildErrorResponseData(trans('responses.not_found'));

        $response = $this->getJson($url);
        $response->assertNotFound();

        $response->assertExactJson($expected);
    }

    /**
     * Получение пустого списка подписок
     *
     * @test
     */
    public function get_empty_subscriptions()
    {
        $subscriber = factory(User::class)->create();

        $url = route('api.users.subscriptions', ['id' => $subscriber->id]);

        $response = $this->getJson($url);
        $response->assertOk();

        $response->assertExactJson([]);
    }

    /**
     * Получение списка подписок
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

        $publisherDto1 = new UserDto(
            $publisher1->id, $publisher1->name,
            $publisher1->email, $publisher1->created_at,
            $publisher1->updated_at, $publisher1->subscriptions_count, $publisher1->subscribers_count
        );

        $publisherDto2 = new UserDto(
            $publisher2->id, $publisher2->name,
            $publisher2->email, $publisher2->created_at,
            $publisher2->updated_at, $publisher2->subscriptions_count, $publisher2->subscribers_count
        );

        $expected[] = $publisherDto1->toArray();
        $expected[] = $publisherDto2->toArray();

        $url = route('api.users.subscriptions', ['id' => $subscriber->id]);

        $response = $this->getJson($url);
        $response->assertOk();

        $response->assertExactJson($expected);
    }

    /**
     * Получение подписчиков несуществующего пользователя
     *
     * @test
     */
    public function get_not_existed_user_subscribers()
    {
        $url = route('api.users.subscribers', ['id' => 999]);

        $expected = $this->buildErrorResponseData(trans('responses.not_found'));

        $response = $this->getJson($url);
        $response->assertNotFound();

        $response->assertExactJson($expected);
    }

    /**
     * Получение пустого списка подписчиков
     *
     * @test
     */
    public function get_empty_subscribers()
    {
        $publisher = factory(User::class)->create();

        $url = route('api.users.subscribers', ['id' => $publisher->id]);

        $response = $this->getJson($url);
        $response->assertOk();

        $response->assertExactJson([]);
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

        $subscriberDto1 = new UserDto(
            $subscriber1->id, $subscriber1->name,
            $subscriber1->email, $subscriber1->created_at,
            $subscriber1->updated_at, $subscriber1->subscriptions_count, $subscriber1->subscribers_count
        );

        $subscriberDto2 = new UserDto(
            $subscriber2->id, $subscriber2->name,
            $subscriber2->email, $subscriber2->created_at,
            $subscriber2->updated_at, $subscriber2->subscriptions_count, $subscriber2->subscribers_count
        );

        $expected[] = $subscriberDto1->toArray();
        $expected[] = $subscriberDto2->toArray();

        $url = route('api.users.subscribers', ['id' => $publisher->id]);

        $response = $this->getJson($url);
        $response->assertOk();

        $response->assertExactJson($expected);
    }
}
