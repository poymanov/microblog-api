<?php

namespace Tests\Unit\DtoFactories;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuccessfulResponseDtoFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ответ "Успешная регистрация"
     *
     * @test
     */
    public function successful_registration()
    {
        $expected = $this->buildResponseData(trans('responses.successfully_signup.message'));

        $dto = SuccessfulResponseDtoFactory::buildSuccessfulSignup();
        $this->assertEquals($expected, $dto->toArray());
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
        $this->assertEquals($expected, $dto->toArray());
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
        $this->assertEquals($expected, $dto->toArray());
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
        $this->assertEquals($expected, $dto->toArray());
    }

    /**
     * Ответ "Успешная авторизация"
     *
     * @test
     */
    public function successful_login()
    {
        $this->createOauthClient();

        $user = factory(User::class)->create();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addHour();
        $token->save();

        $expected = [
            'data' => [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
            ]
        ];

        $dto = SuccessfulResponseDtoFactory::buildSuccessfulLogin($tokenResult);
        $this->assertEquals($expected, $dto->toArray());
    }
}
