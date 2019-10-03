<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Response;
use Validator;

class AuthController extends Controller
{
    private $service;

    /**
     * @OA\Info(title="Microblog API", version="0.1")
     *
     * @OA\Tag(
     *     name="auth",
     *     description="Доступ к API",
     * )
     *
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     * ),
     */
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
    /**
     * @OA\Post(
     *     path="/api/auth/signup",
     *     tags={"auth"},
     *     summary="Регистрация пользователя",
     *     @OA\Response(response="201", description="Успешная регистрация",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="User created"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Validation failed"),
     *                  @OA\Property(property="errors", type="object",
     *                      @OA\Property(property="name", type="array",
     *                          @OA\Items(type="string", example="The name field is required.")
     *                      ),
     *                      @OA\Property(property="email", type="array",
     *                          @OA\Items(type="string", example="The email field is required.")
     *                      ),
     *                      @OA\Property(property="password", type="array",
     *                          @OA\Items(type="string", example="The password field is required.")
     *                      ),
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Parameter(name="name", in="query", required=true, description="Имя пользователя", @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", required=true, description="Email пользователя", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password", in="query", required=true, description="Пароль", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password_confirmation", in="query", required=true, description="Подтверждение пароля", @OA\Schema(type="string")),

     * )
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
            $failedValidation = $this->service->getFailedValidationResponseData($validator->errors()->toArray());
            return response()->json($failedValidation, Response::HTTP_UNPROCESSABLE_ENTITY);
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
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"auth"},
     *     summary="Аутентификация пользователя",
     *     @OA\Response(response="200", description="Успешная аутентификация",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="access_token", type="string", example="eyJ0eXAiO..."),
     *                  @OA\Property(property="token_type", type="string", example="Bearer"),
     *                  @OA\Property(property="expires_at", type="string", example="2019-02-06 22:00:53"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Validation failed"),
     *                  @OA\Property(property="errors", type="object",
     *                      @OA\Property(property="email", type="array",
     *                          @OA\Items(type="string", example="The email field is required.")
     *                      ),
     *                      @OA\Property(property="password", type="array",
     *                          @OA\Items(type="string", example="The password field is required.")
     *                      ),
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="401", description="Попытка аутентификации с отсутствующими в БД данными",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Unauthorized"),
     *                  @OA\Property(property="errors", type="string", example="Failed to authorize user (unknown user or invalid email/password)"),
     *              ),
     *          ),
     *     ),
     *     @OA\Parameter(name="email", in="query", required=true, description="Email пользователя", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password", in="query", required=true, description="Пароль", @OA\Schema(type="string")),
     * )
     */
    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Проверка правильности полученных данных
        if ($validator->fails()) {
            $failedValidation = $this->service->getFailedValidationResponseData($validator->errors()->toArray());
            return response()->json($failedValidation, Response::HTTP_UNPROCESSABLE_ENTITY);
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
    /**
     * @OA\Get(
     *     path="/api/auth/logout",
     *     tags={"auth"},
     *     summary="Завершение сеанса авторизации пользователя",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="Успешное завершение сеанса",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Successfully logged out"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="401", description="Попытка завершения сеанса неавторизованным пользователем",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Unauthorized"),
     *                  @OA\Property(property="errors", type="string", example="Failed to logout"),
     *              ),
     *          ),
     *     ),
     * ),
     */
    public function logout()
    {
        request()->user()->token()->revoke();
        $successfullyLogoutResponseData = $this->service->getSuccessfullyLogoutResponseData();
        return response()->json($successfullyLogoutResponseData);
    }
}
