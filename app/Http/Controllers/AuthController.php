<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\User;
use Hash;
use Illuminate\Http\Response;
use Validator;
use Auth;

class AuthController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new AuthService();
        $this->middleware('auth:api')->only('logout');
    }

    /**
     * Авторизация пользователей
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup()
    {
        $validator = Validator::make(request()->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Проверка правильности полученных данных
        if ($validator->fails()) {
            $responseData = $this->service->getFailedValidationResponseData($validator->errors());
            return response()->json($responseData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'name' => request()->name,
            'email' => request()->email,
            'password' => Hash::make(request()->password),
        ]);

        $user->save();

        $responseData = $this->service->getSuccessfullySignupResponseData();
        return response()->json($responseData, Response::HTTP_CREATED);
    }

    /**
     * Авторизация пользователя
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Проверка правильности полученных данных
        if ($validator->fails()) {
            $responseData = $this->service->getFailedValidationResponseData($validator->errors());
            return response()->json($responseData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = request(['email', 'password']);
        // Попытка авторизации с использованием данных из запроса
        if(! Auth::attempt($credentials)) {
            $responseData = $this->service->getUnauthorizedResponseData();
            return response()->json($responseData, Response::HTTP_UNAUTHORIZED);
        }

        /** @var User $user */
        $user = request()->user();

        // Создание токена доступа
        $token = $this->service->createToken($user);
        $successResponseData = $this->service->getSuccessAuthResponseData($token);

        // Возвращение ответа с токеном
        return response()->json($successResponseData);
    }

    /**
     * Завершение сеанса авторизации пользователя
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        request()->user()->token()->revoke();
        $successfullyLogoutResponseData = $this->service->getSuccessfullyLogoutResponseData();
        return response()->json($successfullyLogoutResponseData);
    }
}
