<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class UsersController extends Controller
{
    /** @var UserService */
    private $service;

    /**
     * @OA\Tag(
     *     name="users",
     *     description="Работа с пользователями",
     * )
     *
     * @OA\Schema(
     *     schema="UserProfileResponse",
     *     title="User Profile Response",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="User"),
     *     @OA\Property(property="email", type="string", example="test@test.ru"),
     *     @OA\Property(property="created_at", type="integer", example=1550087394),
     *     @OA\Property(property="updated_at", type="integer", example=1550087394),
     *     @OA\Property(property="subscriptions_count", type="integer", example=1),
     *     @OA\Property(property="subscribers_count", type="integer", example=2),
     * )
     *
     * @OA\Schema(
     *     schema="UpdateProfileRequestBody",
     *     title="Update Profile Request Body",
     *     required={"name"},
     *     @OA\Property(property="name", type="string", example="test@test.ru", description="Имя пользователя", maxLength=255),
     *     @OA\Property(property="password", type="string", example="123qwe", description="Пароль", minLength=6),
     *     @OA\Property(property="password_confirmation", type="string", example="123qwe", description="Подтверждение пароля", minLength=6),
     * )
     */
    /**
     * UsersController constructor.
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api')->only(['update']);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"users"},
     *     summary="Получение профиля пользователя",
     *     @OA\Response(response="200", description="Успешное получение профиля",
     *          @OA\JsonContent(ref="#/components/schemas/UserProfileResponse")
     *     ),
     *     @OA\Response(response="404", description="Попытка получения профиля несуществующего пользователя",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found"),
     *         ),
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, description="Идентификатор пользователя", @OA\Schema(type="integer")),
     * )
     */
    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     */
    public function show(int $id)
    {
        $user = $this->service->getById($id);

        return response()->json($user->toArray());
    }

    /**
     * @OA\Patch(
     *     path="/api/users",
     *     tags={"users"},
     *     summary="Редактирование профиля авторизованного пользователя",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProfileRequestBody")
     *     ),
     *     @OA\Response(response="200", description="Профиль отредактирован",
     *          @OA\JsonContent(ref="#/components/schemas/UserProfileResponse")
     *     ),
     *     @OA\Response(response="403", description="Попытка получения профиля неавторизованным пользователем",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Access denied"),
     *              @OA\Property(property="errors", type="array",
     *                  @OA\Items(type="string", example="You have not access permission to API.")
     *              ),
     *         ),
     *     ),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="text", type="array",
     *                      @OA\Items(type="string", example="The name field is required.")
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

    /**
     * @OA\Get(
     *     path="/api/users/{id}/subscriptions",
     *     tags={"users"},
     *     summary="Получение подписок пользователя",
     *     @OA\Response(response="200", description="Успешное получение подписок",
     *          @OA\JsonContent(@OA\Items(ref="#/components/schemas/UserProfileResponse"))
     *     ),
     *     @OA\Response(response="404", description="Попытка получения подписок несуществующего пользователя",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found"),
     *         ),
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, description="Идентификатор пользователя", @OA\Schema(type="integer")),
     * )
     */
    /**
     * Получение списка подписок
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     */
    public function subscriptions(int $id)
    {
        $subscriptions = $this->service->getSubscriptionsExtracted($id);
        return response()->json($subscriptions);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/subscribers",
     *     tags={"users"},
     *     summary="Получение подписчиков пользователя",
     *     @OA\Response(response="200", description="Успешное получение подписчиков",
     *          @OA\JsonContent(@OA\Items(ref="#/components/schemas/UserProfileResponse"))
     *     ),
     *     @OA\Response(response="404", description="Попытка получения подписчиков несуществующего пользователя",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found"),
     *         ),
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, description="Идентификатор пользователя", @OA\Schema(type="integer")),
     * )
     */
    /**
     * Получение списка подписчиков
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     */
    public function subscribers(int $id)
    {
        $subscriptions = $this->service->getSubscribersExtracted($id);
        return response()->json($subscriptions);
    }
}
