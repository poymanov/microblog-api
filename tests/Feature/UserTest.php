<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
