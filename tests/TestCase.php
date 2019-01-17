<?php

namespace Tests;

use App\Services\AuthService;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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

    protected function getAccessDeniedResponseData()
    {
        $authService = new AuthService();
        return $authService->getAccessDeniedResponseData();
    }
}
