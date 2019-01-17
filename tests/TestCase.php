<?php

namespace Tests;

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
        Passport::actingAs(factory(User::class)->create());
    }
}
