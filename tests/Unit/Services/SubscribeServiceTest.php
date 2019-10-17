<?php

namespace Tests\Unit\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\UserSubscribeHimselfException;
use App\Exceptions\UserUnsubscribeFromNotSubscribedUser;
use App\Services\SubscribeService;
use App\User;
use App\UserSubscribe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var SubscribeService
     */
    private $service;

    /**
     * SubscribeServiceTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(SubscribeService::class);
    }

    /**
     * Подписчик не существует
     *
     * @test
     * @throws NotFoundException
     * @throws UserSubscribeHimselfException
     * @throws \App\Exceptions\ValidationException
     */
    public function subscriber_not_existed()
    {
        $this->expectException(NotFoundException::class);

        $publisher = factory(User::class)->create();

        $this->service->subscribe(999, $publisher->id);
    }

    /**
     * Подписка на несуществующего пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws UserSubscribeHimselfException
     * @throws \App\Exceptions\ValidationException
     */
    public function publisher_not_existed()
    {
        $this->expectException(NotFoundException::class);

        $subscriber = factory(User::class)->create();

        $this->service->subscribe($subscriber->id, 999);
    }

    /**
     * Подписка на самого себя
     *
     * @test
     * @throws NotFoundException
     * @throws UserSubscribeHimselfException
     * @throws \App\Exceptions\ValidationException
     */
    public function user_subscribe_to_himself()
    {
        $this->expectException(UserSubscribeHimselfException::class);

        $subscriber = factory(User::class)->create();

        $this->service->subscribe($subscriber->id, $subscriber->id);
    }

    /**
     * Подписка на другого пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws UserSubscribeHimselfException
     * @throws \App\Exceptions\ValidationException
     */
    public function user_subscribe_to_another_user()
    {
        $subscriber = factory(User::class)->create();
        $publisher = factory(User::class)->create();

        $this->service->subscribe($subscriber->id, $publisher->id);

        $this->assertDatabaseHas('user_subscribes', [
            'subscriber_id' => $subscriber->id, 'publisher_id' => $publisher->id
        ]);
    }

    /**
     * Несуществующий пользователь пытается отписаться
     *
     * @test
     * @throws NotFoundException
     * @throws UserUnsubscribeFromNotSubscribedUser
     */
    public function unknown_user_unsubscribe()
    {
        $this->expectException(NotFoundException::class);

        $publisher = factory(User::class)->create();

        $this->service->unsubscribe(999, $publisher->id);
    }

    /**
     * Отписка от несуществующего пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws UserUnsubscribeFromNotSubscribedUser
     */
    public function user_unsubscribe_from_not_existed_user()
    {
        $this->expectException(NotFoundException::class);

        $subscriber = factory(User::class)->create();

        $this->service->unsubscribe($subscriber->id, 999);
    }

    /**
     * Отписка от пользователя, на которого не было подписки
     *
     * @test
     * @throws NotFoundException
     * @throws UserUnsubscribeFromNotSubscribedUser
     */
    public function user_unsubscribe_from_not_subscribed_user()
    {
        $this->expectException(UserUnsubscribeFromNotSubscribedUser::class);

        $subscriber = factory(User::class)->create();
        $publisher = factory(User::class)->create();

        $this->service->unsubscribe($subscriber->id, $publisher->id);
    }

    /**
     * Отписка от пользователя
     *
     * @test
     * @throws NotFoundException
     * @throws UserUnsubscribeFromNotSubscribedUser
     */
    public function user_unsubscribe_from_another_user()
    {
        $subscriber = factory(User::class)->create();
        $publisher = factory(User::class)->create();

        factory(UserSubscribe::class)->create([
            'subscriber_id' => $subscriber->id, 'publisher_id' => $publisher->id
        ]);

        $this->service->unsubscribe($subscriber->id, $publisher->id);

        $this->assertDatabaseMissing('user_subscribes', [
            'subscriber_id' => $subscriber->id, 'publisher_id' => $publisher->id
        ]);
    }
}
