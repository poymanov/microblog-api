<?php

namespace Tests\Unit\DtoFactories;

use App\Dto\factories\ErrorResponseDtoFactory;
use Tests\TestCase;

class ErrorResponseDtoFactoryTest extends TestCase
{
    /**
     * Создание ответа "Доступ запрещен"
     *
     * @test
     */
    public function response_access_denied()
    {
        $expected = [
            'data' => [
                'message' => trans('responses.access_denied.message'),
                'errors' => trans('responses.access_denied.errors'),
            ],
        ];

        $dto = ErrorResponseDtoFactory::buildAccessDenied();
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание ответа "Неавторизованный доступ"
     *
     * @test
     */
    public function response_unauthorized()
    {
        $expected = [
            'data' => [
                'message' => trans('responses.unauthorized.message'),
                'errors' => trans('responses.unauthorized.errors'),
            ],
        ];

        $dto = ErrorResponseDtoFactory::buildUnauthorized();
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание ответа "Попытка завершения сеанса неавторизованным пользователем"
     *
     * @test
     */
    public function response_unauthorized_logout()
    {
        $expected = [
            'data' => [
                'message' => trans('responses.unauthorized_logout.message'),
                'errors' => trans('responses.unauthorized_logout.errors'),
            ],
        ];

        $dto = ErrorResponseDtoFactory::buildUnauthorizedLogout();
        $this->assertEquals($dto->toArray(), $expected);
    }
}
