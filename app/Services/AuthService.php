<?php

namespace App\Services;

use App\User;
use Carbon\Carbon;

class AuthService
{
    /**
     * Описание сообщений для формирования ответов
     */
    const RESPONSE_DATA = [
        'VALIDATION_FAILED' => [
            'MESSAGE' => 'Validation failed',
        ],
        'UNAUTHORIZED' => [
            'MESSAGE' => 'Unauthorized',
            'ERRORS' => 'Failed to authorize user (unknown user or invalid email/password)'
        ],
        'ACCESS_DENIED' => [
            'MESSAGE' => 'Access denied',
            'ERRORS' => 'You have not access permission to API',
        ],
        'UNAUTHORIZED_LOGOUT' => [
            'MESSAGE' => 'Unauthorized',
            'ERRORS' => 'Failed to logout',
        ],
        'SUCCESSFULLY_LOGOUT' => [
            'MESSAGE' => 'Successfully logged out',
        ],
        'SUCCESSFULLY_SIGNUP' => [
            'MESSAGE' => 'User created'
        ]
    ];

    /**
     * Создание токена доступа для пользователя
     *
     * @param User $user
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function createToken(User $user)
    {
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addHour();
        $token->save();

        return $tokenResult;
    }

    /**
     * Данные для ответа в json: успешная авторизация
     *
     * @param $token
     * @return array
     */
    public function getSuccessAuthResponseData($token)
    {
        return [
            'data' => [
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString(),
            ]
        ];
    }

    /**
     * Данные для ответа в json: успешное завершение сеанса авторизации пользователя
     *
     * @return array
     */
    public function getSuccessfullyLogoutResponseData()
    {
        return [
            'data' => [
                'message' => self::RESPONSE_DATA['SUCCESSFULLY_LOGOUT']['MESSAGE'],
            ]
        ];
    }

    /**
     * Данные для ответа в json: успешное завершения сеанса регистрации пользователя
     *
     * @return array
     */
    public function getSuccessfullySignupResponseData()
    {
        return [
            'data' => [
                'message' => self::RESPONSE_DATA['SUCCESSFULLY_SIGNUP']['MESSAGE'],
            ]
        ];
    }

    /**
     * Данные для ответа в json: пользователю запрещен доступ к API
     *
     * @return array
     */
    public function getAccessDeniedResponseData()
    {
        return $this->getErrorResponseScheme(
            self::RESPONSE_DATA['ACCESS_DENIED']['MESSAGE'],
            self::RESPONSE_DATA['ACCESS_DENIED']['ERRORS']
        );
    }

    /**
     * Данные для ответа в json: данные переданы в неверном формате или не указаны
     *
     * @param $errors
     * @return array
     */
    public function getFailedValidationResponseData($errors)
    {
        return $this->getErrorResponseScheme(self::RESPONSE_DATA['VALIDATION_FAILED']['MESSAGE'], $errors);
    }

    /**
     * Данные для ответа в json: переданы неправильные данные для авторизации
     *
     * @return array
     */
    public function getUnauthorizedResponseData()
    {
        return $this->getErrorResponseScheme(
            self::RESPONSE_DATA['UNAUTHORIZED']['MESSAGE'],
            self::RESPONSE_DATA['UNAUTHORIZED']['ERRORS']
        );
    }

    /**
     * Данные для ответа в json: попытка прекратить пользовательский сеанс неавторизованным пользователем
     * @return array
     */
    public function getUnauthorizedLogoutReponseData()
    {
        return $this->getErrorResponseScheme(
            self::RESPONSE_DATA['UNAUTHORIZED_LOGOUT']['MESSAGE'],
            self::RESPONSE_DATA['UNAUTHORIZED_LOGOUT']['ERRORS']
        );
    }

    /**
     * Базовая структура данных ответа для json
     *
     * @param $message
     * @param $errors
     * @return array
     */
    private function getErrorResponseScheme($message, $errors)
    {
        return [
            'data' => [
                'message' => $message,
                'errors' => $errors,
            ],
        ];
    }
}
