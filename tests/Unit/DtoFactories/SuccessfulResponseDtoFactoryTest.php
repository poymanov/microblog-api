<?php

namespace Tests\Unit\DtoFactories;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use Tests\TestCase;

class SuccessfulResponseDtoFactoryTest extends TestCase
{
    /**
     * Ответ "Успешная регистрация"
     *
     * @test
     */
    public function successful_registration()
    {
        $expected = $this->buildResponseData(trans('responses.successfully_signup.message'));

        $dto = SuccessfulResponseDtoFactory::buildSuccessfulSignup();
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Ответ "Успешное завершение сеанса пользователя"
     *
     * @test
     */
    public function successful_logout()
    {
        $expected = $this->buildResponseData(trans('responses.successfully_logout.message'));

        $dto = SuccessfulResponseDtoFactory::buildSuccessfulLogout();
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Ответ "Успешное создание объекта"
     *
     * @test
     */
    public function successful_created()
    {
        $expected = $this->buildResponseData(trans('responses.successfully_created'));

        $dto = SuccessfulResponseDtoFactory::buildSuccessfulCreated();
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Ответ "Успешное удаление объекта"
     *
     * @test
     */
    public function successful_deleted()
    {
        $expected = $this->buildResponseData(trans('responses.successfully_deleted'));

        $dto = SuccessfulResponseDtoFactory::buildSuccessfulDeleted();
        $this->assertEquals($dto->toArray(), $expected);
    }
}
