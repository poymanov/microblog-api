<?php

namespace Tests\Unit\DtoFactories;

use App\Dto\factories\ValidationDtoFactory;
use Tests\TestCase;

class ValidationDtoFactoryTest extends TestCase
{
    /**
     * Создание dto с ошибками валидации
     *
     * @test
     */
    public function failed_validation_dto()
    {
        $errors = [
            'name' => 'The text field is required.',
        ];

        $expected = [
            'data' => [
                'message' => trans('responses.validation_failed'),
                'errors' => $errors,
            ],
        ];

        $dto = ValidationDtoFactory::buildFailed($errors);
        $this->assertTrue($dto->isFailed());
        $this->assertFalse($dto->isOk());
        $this->assertEquals($dto->toArray(), $expected);
    }

    /**
     * Создание dto с успешно прошедшей валидации
     *
     * @test
     */
    public function successful_validation_dto()
    {
        $expected = [
            'data' => [
                'message' => trans('responses.successfully_created'),
                'errors' => [],
            ],
        ];

        $dto = ValidationDtoFactory::buildOk();
        $this->assertTrue($dto->isOk());
        $this->assertFalse($dto->isFailed());
        $this->assertEquals($dto->toArray(), $expected);
    }
}
