<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\Response;

class PostsController extends Controller
{
    private $service;

    /**
     * PostsController constructor.
     */
    /**
     * @OA\Tag(
     *     name="post",
     *     description="Работа с публикациями",
     * )
     *
     * @OA\Schema(
     *     schema="UsersPosts",
     *     title="Users Posts Response",
     *          @OA\Property(
     *              property="id",
     *              type="integer",
     *              example=1,
     *          ),
     *         @OA\Property(
     *              property="text",
     *              type="string",
     *              example="text text text",
     *         ),
     *         @OA\Property(
     *              property="created_at",
     *              type="integer",
     *              example=1550087394,
     *         ),
     * )
     */
    public function __construct()
    {
        $this->service = new PostService();
        $this->middleware('auth:api')->only(['store', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"post"},
     *     summary="Получение публикаций пользователя",
     *     @OA\Response(response="200", description="Успешное получение списка публикаций пользователя",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Items(ref="#/components/schemas/UsersPosts")
     *                  ),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="first", type="string", example="http://microblog-api.test/api/posts/1?page=1"),
     *                  @OA\Property(property="last", type="string", example="http://microblog-api.test/api/posts/1?page=1"),
     *                  @OA\Property(property="prev", example=null),
     *                  @OA\Property(property="next", example=null),
     *              ),
     *              @OA\Property(property="meta", type="object",
     *                  @OA\Property(property="current_page", type="int", example=1),
     *                  @OA\Property(property="from", type="int", example=1),
     *                  @OA\Property(property="last_page", type="int", example=1),
     *                  @OA\Property(property="path", type="string", example="http://microblog-api.test/api/posts/1"),
     *                  @OA\Property(property="per_page", type="int", example=10),
     *                  @OA\Property(property="to", type="int", example=1),
     *                  @OA\Property(property="total", type="int", example=1),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="404", description="Попытка получения публикаций для несуществующего пользователя",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Resource not found"),
     *              ),
     *         ),
     *     ),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Validation failed"),
     *                  @OA\Property(property="errors", type="object",
     *                      @OA\Property(property="text", type="array",
     *                          @OA\Items(type="string", example="The text field is required.")
     *                      ),
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, description="Идентификатор публикации", @OA\Schema(type="integer")),
     * )
     */
    /**
     * Список публикаций пользователя
     *
     * @param int $userId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \App\Exceptions\NotFoundException
     */
    public function index(int $userId)
    {
        return PostResource::collection($this->service->getUserPosts($userId));
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"post"},
     *     summary="Создание публикации",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="201", description="Успешное создание публикации",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Successfully created"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="401", description="Попытка создания публикации без авторизации",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Access denied"),
     *                  @OA\Property(property="errors", type="string", example="You have not access permission to API"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="422", description="Ошибки валидации параметров",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Validation failed"),
     *                  @OA\Property(property="errors", type="object",
     *                      @OA\Property(property="text", type="array",
     *                          @OA\Items(type="string", example="The text field is required.")
     *                      ),
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Parameter(name="text", in="query", required=true, description="Текст публикации", @OA\Schema(type="string", maxLength=300)),
     * )
     */
    /**
     * Создание публикации
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidationException
     * @throws \App\Exceptions\NotFoundException
     */
    public function store()
    {
        $validationResult = $this->service->createPost(request()->all(), request()->user()->id);

        return response()->json($validationResult->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     tags={"post"},
     *     summary="Удаление публикации",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="Успешное удаление публикации",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Successfully deleted"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(response="401", description="Попытка удаления публикации без авторизации / удаления чужой публикации",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Access denied"),
     *                  @OA\Property(property="errors", type="string", example="You have not access permission to API"),
     *              ),
     *          ),
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, description="Идентификатор публикации", @OA\Schema(type="integer")),
     * )
     */
    /**
     * Удаление публикации
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\AccessDeniedException
     * @throws \App\Exceptions\NotFoundException
     */
    public function destroy(int $id)
    {
        $deletingResult = $this->service->deletePost($id, request()->user()->id);

        return response()->json($deletingResult->toArray(), Response::HTTP_OK);
    }
}
