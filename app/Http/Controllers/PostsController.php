<?php

namespace App\Http\Controllers;

use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Services\PostService;
use Illuminate\Http\Response;

class PostsController extends Controller
{
    /** @var PostService */
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
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="text", type="string", example="text text text"),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="created_at", type="integer", example=1550087394),
     *     @OA\Property(property="updated_at", type="integer", example=1550087394),
     * )
     * @OA\Schema(
     *     schema="CreatePostRequestBody",
     *     title="New Post Request Body",
     *     required={"text"},
     *     @OA\Property(property="text", type="string", example="Post text", maxLength=300),
     * )
     */
    /**
     * PostsController constructor.
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->service = $postService;
        $this->middleware('auth:api')->only(['store', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"post"},
     *     summary="Получение публикаций пользователя",
     *     @OA\Response(response="200", description="Успешное получение списка публикаций пользователя",
     *         @OA\JsonContent(
     *              @OA\Items(ref="#/components/schemas/UsersPosts")
     *         ),
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
     *     @OA\Parameter(name="id", in="path", required=true, description="Идентификатор пользователя", @OA\Schema(type="integer")),
     * )
     */
    /**
     * Список публикаций пользователя
     *
     * @param int $userId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws NotFoundException
     */
    public function index(int $userId)
    {
        return response()->json($this->service->getUserPostsExtracted($userId));
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"post"},
     *     summary="Создание публикации",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreatePostRequestBody")
     *     ),
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
        $post = $this->service->createPost(request()->all(), request()->user()->id);

        return response()->json($post, Response::HTTP_CREATED);
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
     * @throws AccessDeniedException
     * @throws NotFoundException
     */
    public function destroy(int $id)
    {
        $this->service->deletePost($id, request()->user()->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
