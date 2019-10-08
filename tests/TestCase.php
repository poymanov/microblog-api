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
    protected function authApi(): User
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
    protected function getAccessDeniedResponseData(): array
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

    /**
     * Создание базовой структуры для примера ответа
     *
     * @param $message
     * @return array
     */
    protected function buildResponseData($message): array
    {
        return [
            'data' => [
                'message' => $message,
            ]
        ];
    }


    /**
     * Создание базовой структуры для примера ответа c описанием ошибки
     *
     * @param string $message
     * @param array $errors
     * @return array
     */
    protected function buildErrorResponseData(string $message, array $errors = []): array
    {
        return [
            'data' => [
                'message' => $message,
                'errors' => $errors,
            ],
        ];
    }
}
