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
        $expected = $this->buildErrorResponseData(trans('responses.not_found'), []);

        $dto = ErrorResponseDtoFactory::buildNotFound();
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

        $expected = $this->buildErrorResponseData(trans('responses.something_went_wrong'), $errors);

        $dto = ErrorResponseDtoFactory::buildSomethingWentWrong($errors);
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание ответа "Пользователь пытается подписаться на самого себя"
     *
     * @test
     */
    public function response_user_subscribe_himself()
    {
        $expected = $this->buildErrorResponseData(trans('responses.user_subscribe_himself'), []);

        $dto = ErrorResponseDtoFactory::buildSubscribeHimself();
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание ответа "Попытка отписаться от пользователя, на которого не подписан"
     *
     * @test
     */
    public function response_user_unsubscribe_from_not_subscribed_user()
    {
        $expected = $this->buildErrorResponseData(trans('responses.user_unsubscribe_from_not_subscribed'), []);

        $dto = ErrorResponseDtoFactory::buildUnsubscribeFromNotSubscribed();
        $this->assertEquals($dto->toArray(), $expected);
    }
}
