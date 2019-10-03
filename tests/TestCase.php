<?php

namespace Tests;

use App\Services\AuthService;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Авторизация пользователя для API
     */
    protected function authApi()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        return $user;
    }

    /**
     * Ответ для неавторизованного доступа
     *
     * @return array
     */
    protected function getAccessDeniedResponseData()
    {
        $authService = new AuthService();
        return $authService->getAccessDeniedResponseData()->toArray();
    }

    /**
     * Создание oauth-клиента
     */
    protected function createOauthClient()
    {
        $client = (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', ""
        );

        Passport::actingAsClient($client);
    }
}
