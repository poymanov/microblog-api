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
        $this->assertEmpty($dto->getData());
    }

    /**
     * Создание dto с успешно прошедшей валидации
     *
     * @test
     */
    public function successful_validation_dto()
    {
        $data = [
            'name' => 'test',
        ];

        $expected = [
            'data' => [
                'message' => null,
                'errors' => [],
            ],
        ];

        $dto = ValidationDtoFactory::buildOk($data);
        $this->assertTrue($dto->isOk());
        $this->assertFalse($dto->isFailed());
        $this->assertEquals($dto->toArray(), $expected);
        $this->assertEquals($dto->getData(), $data);
    }
}
