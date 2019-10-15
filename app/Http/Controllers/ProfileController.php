<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class ProfileController extends Controller
{
    /** @var UserService */
    private $service;

    /**
     * @OA\Tag(
     *     name="profile",
     *     description="Работа с профилем пользователя",
     * )
     *
     * @OA\Schema(
     *     schema="UserProfile",
     *     title="User Profile Response",
     *          @OA\Property(
     *              property="id",
     *              type="integer",
     *              example=1,
     *          ),
     *         @OA\Property(
     *              property="name",
     *              type="string",
     *              example="User",
     *         ),
     *         @OA\Property(
     *              property="email",
     *              type="string",
     *              example="test@test.ru",
     *         ),
     *         @OA\Property(
     *              property="created_at",
     *              type="integer",
     *              example=1550087394,
     *         ),
     *         @OA\Property(
     *              property="updated_at",
     *              type="integer",
     *              example=1550087394,
     *         ),
     * )
     */
    /**
     * ProfileController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
        $this->middleware('auth:api')->only(['show', 'update']);
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"profile"},
     *     summary="Получение профиля пользователя",
     *     @OA\Response(response="200", description="Успешное получение профиля",
     *          @OA\JsonContent(ref="#/components/schemas/UserProfile")
     *     ),
     *     @OA\Response(response="403", description="Попытка получения профиля неавторизованным пользователем",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Access denied"),
     *                  @OA\Property(property="errors", type="array",
     *                      @OA\Items(type="string", example="You have not access permission to API.")
     *                  ),
     *              ),
     *         ),
     *     ),
     * )
     */
    /**
     * Просмотр профиля пользователя
     * @throws \App\Exceptions\NotFoundException
     */
    public function show()
    {
        $user = $this->service->getById(request()->user()->id);
        return response()->json($user->toArray());
    }

    /**
     * @OA\Post(
     *     path="/api/profile",
     *     tags={"profile"},
     *     summary="Редактирование профиля пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="test@test.ru"),
     *              @OA\Property(property="password", type="string", example="123qwe"),
     *              @OA\Property(property="password_confirmation", type="string", example="123qwe"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Профиль отредактирован",
     *          @OA\JsonContent(ref="#/components/schemas/UserProfile")
     *     ),
     *     @OA\Response(response="403", description="Попытка получения профиля неавторизованным пользователем",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Access denied"),
     *                  @OA\Property(property="errors", type="array",
     *                      @OA\Items(type="string", example="You have not access permission to API.")
     *                  ),
     *              ),
     *         ),
     *     ),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Validation failed"),
     *                  @OA\Property(property="errors", type="object",
     *                      @OA\Property(property="text", type="array",
     *                          @OA\Items(type="string", example="The name field is required.")
     *                      ),
     *                  ),
     *              ),
     *          ),
     *     ),
     * )
     */
    /**
     * Редактирование профиля пользователя
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\ValidationException
     */
    public function update()
    {
        $user = $this->service->updateUser(request()->all(), request()->user()->id);
        return response()->json($user->toArray());
    }
}
