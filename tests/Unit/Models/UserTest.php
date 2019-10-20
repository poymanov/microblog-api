<?php

namespace Tests\Unit\Models;

use App\User;
use App\UserSubscribe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

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

        $this->assertEquals($publisher1->id, $subscriber->subscriptions->first()->id);
        $this->assertEquals($publisher2->id, $subscriber->subscriptions->last()->id);
    }

    /**
     * Получение подписчиков
     *
     * @test
     */
    public function get_subscriber()
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

        $this->assertEquals($subscriber1->id, $publisher->subscribers->first()->id);
        $this->assertEquals($subscriber2->id, $publisher->subscribers->last()->id);
    }

    /**
     * Получение количества подписок
     *
     * @test
     */
    public function get_subscriptions_count()
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

        $this->assertEquals(2, $subscriber->subscriptions_count);
    }

    /**
     * Получение количества подписчиков
     *
     * @test
     */
    public function get_subscribers_count()
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

        $this->assertEquals(2, $publisher->subscribers_count);
    }
}
