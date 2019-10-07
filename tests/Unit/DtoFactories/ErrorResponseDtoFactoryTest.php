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
        $expected = $this->buildErrorResponseData(
            trans('responses.access_denied.message'),
            [trans('responses.access_denied.errors')]
        );

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
        $expected = $this->buildErrorResponseData(
            trans('responses.unauthorized.message'),
            [trans('responses.unauthorized.errors')]
        );

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
        $expected = $this->buildErrorResponseData(
            trans('responses.unauthorized_logout.message'),
            [trans('responses.unauthorized_logout.errors')]
        );

        $dto = ErrorResponseDtoFactory::buildUnauthorizedLogout();
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание ответа "Ошибка валидации"
     *
     * @test
     */
    public function response_validation_failed()
    {
        $errors = [
            'name' => 'The text field is required.',
        ];

        $expected = $this->buildErrorResponseData(trans('responses.validation_failed'), $errors);

        $dto = ErrorResponseDtoFactory::buildValidationFailed($errors);
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание ответа "Объект не найден"
     *
     * @test
     */
    public function response_not_found()
    {
        $errors = [
            'id' => 1,
        ];

        $expected = $this->buildErrorResponseData(trans('responses.not_found'), $errors);

        $dto = ErrorResponseDtoFactory::buildNotFound($errors);
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание ответа "Ошибка без категории"
     *
     * @test
     */
    public function response_something_went_wrong()
    {
        $errors = ['Critical error'];

        $expected = $this->buildErrorResponseData(trans('something_went_wrong'), $errors);

        $dto = ErrorResponseDtoFactory::buildSomethingWentWrong($errors);
        $this->assertEquals($dto->toArray(), $expected);
    }
}
