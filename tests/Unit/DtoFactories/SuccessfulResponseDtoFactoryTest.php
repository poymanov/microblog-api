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
        $expected = [
            'data' => [
                'message' => trans('responses.successfully_signup.message'),
            ],
        ];

        $dto = SuccessfulResponseDtoFactory::buildSuccessfulSignup();
        $this->assertEquals($dto->toArray(), $expected);
    }
}
