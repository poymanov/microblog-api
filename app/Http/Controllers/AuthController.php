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
     */
    public function __construct()
    {
        $this->service = app(UserService::class);
        $this->middleware('auth:api')->only('logout');
    }

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
    /**
     * Авторизация пользователей
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function signup()
    {
        $responseData = $this->service->registerUser(request()->all());
        return response()->json($responseData->toArray(), Response::HTTP_CREATED);
    }

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
