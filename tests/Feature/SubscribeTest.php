<?php

namespace Tests\Feature;

use App\User;
use App\UserSubscribe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Неавторизованный пользователь не может подписаться
     *
     * @test
     */
    public function unauthorized_user_can_not_subscribe()
    {
        $url = route('api.users.subscribe', ['id' => 1]);

        $response = $this->postJson($url);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Попытка подписаться на несуществующего пользователя
     *
     * @test
     */
    public function user_subscribe_to_not_existed_user()
    {
        $this->authApi();

        $expected = $this->buildErrorResponseData(trans('responses.not_found'));

        $url = route('api.users.subscribe', ['id' => 999]);

        $response = $this->postJson($url);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertExactJson($expected);
    }

    /**
     * Попытка подписаться на самого себя
     *
     * @test
     */
    public function user_subscribe_to_himself()
    {
        $subscriber = $this->authApi();

        $expected = $this->buildErrorResponseData(trans('responses.user_subscribe_himself'));

        $url = route('api.users.subscribe', ['id' => $subscriber->id]);

        $response = $this->postJson($url);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertExactJson($expected);
    }

    /**
     * Подписка на другого пользователя
     *
     * @test
     */
    public function user_subscribe_to_another_user()
    {
        $this->authApi();

        $publisher = factory(User::class)->create();

        $url = route('api.users.subscribe', ['id' => $publisher->id]);

        $response = $this->postJson($url);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * Неавторизованный пользователь не может отписаться
     *
     * @test
     */
    public function unauthorized_user_can_not_unsubscribe()
    {
        $url = route('api.users.unsubscribe', ['id' => 1]);

        $response = $this->deleteJson($url);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertExactJson($this->getAccessDeniedResponseData());
    }

    /**
     * Попытка отписаться от пользователя который не существует
     *
     * @test
     */
    public function user_unsubscribe_from_not_existed_user()
    {
        $this->authApi();

        $expected = $this->buildErrorResponseData(trans('responses.not_found'));

        $url = route('api.users.unsubscribe', ['id' => 999]);

        $response = $this->deleteJson($url);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertExactJson($expected);
    }

    /**
     * Попытка отписаться от пользователя на которого не подписан
     *
     * @test
     */
    public function user_unsubscribe_from_not_subscribed_user()
    {
        $this->authApi();

        $publisher = factory(User::class)->create();

        $expected = $this->buildErrorResponseData(trans('responses.user_unsubscribe_from_not_subscribed'));

        $url = route('api.users.unsubscribe', ['id' => $publisher->id]);

        $response = $this->deleteJson($url);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertExactJson($expected);
    }

    /**
     * Отписка от другого пользователя
     *
     * @test
     */
    public function user_unsubscribe_from_another_user()
    {
        $subscriber = $this->authApi();
        $publisher = factory(User::class)->create();

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber->id, 'publisher_id' => $publisher->id
        ]);

        $url = route('api.users.unsubscribe', ['id' => $publisher->id]);

        $response = $this->deleteJson($url);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
