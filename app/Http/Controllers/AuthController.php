<?php

namespace App\Http\Controllers;

use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationException;
use App\Services\UserService;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /** @var UserService */
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
     * @OA\Schema(
     *     schema="SignupRequestBody",
     *     title="Signup Request Body",
     *     required={"name", "email", "password", "password_confirmation"},
     *     @OA\Property(property="name", type="string", example="Test", description="Имя пользователя", maxLength=255),
     *     @OA\Property(property="email", type="string", example="test@test.ru", description="Email пользователя", maxLength=255),
     *     @OA\Property(property="password", type="string", example="123qwe", description="Пароль", minLength=6),
     *     @OA\Property(property="password_confirmation", type="string", example="123qwe", description="Подтверждение пароля", minLength=6),
     * ),
     * @OA\Schema(
     *     schema="LoginRequestBody",
     *     title="Login Request Body",
     *     required={"email", "password"},
     *     @OA\Property(property="email", type="string", example="test@test.ru", description="Email пользователя"),
     *     @OA\Property(property="password", type="string", example="123qwe", description="Пароль"),
     * )
     */
    /**
     * AuthController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
        $this->middleware('auth:api')->only('logout');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/signup",
     *     tags={"auth"},
     *     summary="Регистрация пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SignupRequestBody")
     *     ),
     *     @OA\Response(response="201", description="Успешная регистрация"),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array",
     *                      @OA\Items(type="string", example="The name field is required.")
     *                  ),
     *                  @OA\Property(property="email", type="array",
     *                      @OA\Items(type="string", example="The email field is required.")
     *                  ),
     *                  @OA\Property(property="password", type="array",
     *                      @OA\Items(type="string", example="The password field is required.")
     *                  ),
     *              ),
     *          ),
     *     ),
     * )
     */
    /**
     * Авторизация пользователей
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function signup()
    {
        $this->service->registerUser(request()->all());
        return response()->json(null, Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"auth"},
     *     summary="Аутентификация пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequestBody")
     *     ),
     *     @OA\Response(response="200", description="Успешная аутентификация",
     *         @OA\JsonContent(
     *              @OA\Property(property="access_token", type="string", example="eyJ0eXAiO..."),
     *              @OA\Property(property="token_type", type="string", example="Bearer"),
     *              @OA\Property(property="expires_at", type="string", example="2019-02-06 22:00:53"),
     *         ),
     *     ),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="email", type="array",
     *                      @OA\Items(type="string", example="The email field is required.")
     *                  ),
     *                  @OA\Property(property="password", type="array",
     *                      @OA\Items(type="string", example="The password field is required.")
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="401", description="Попытка аутентификации с отсутствующими в БД данными",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized"),
     *              @OA\Property(property="errors", type="string", example="Failed to authorize user (unknown user or invalid email/password)"),
     *          ),
     *     ),
     * )
     */
    /**
     * Авторизация пользователя
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function login()
    {
        $responseData = $this->service->loginUser(request()->all());
        return response()->json($responseData->toArray());
    }

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
     *              @OA\Property(property="message", type="string", example="Successfully logged out"),
     *         ),
     *     ),
     *     @OA\Response(response="401", description="Попытка завершения сеанса неавторизованным пользователем",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized"),
     *              @OA\Property(property="errors", type="string", example="Failed to logout"),
     *          ),
     *     ),
     * ),
     */
    /**
     * Завершение сеанса авторизации пользователя
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $responseData = $this->service->logoutUser();
        return response()->json($responseData->toArray());
    }
}
