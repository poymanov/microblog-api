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
     */
    /**
     * UsersController constructor.
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
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

}
