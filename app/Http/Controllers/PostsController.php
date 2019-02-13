<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use App\Services\PostsService;
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
     *
     *         @OA\Property(
     *              property="text",
     *              type="string",
     *              example="text text text",
     *         ),
     * )
     */
    public function __construct()
    {
        $this->service = new PostsService();
        $this->middleware('auth:api')->only(['store', 'destroy']);
    }

    /**
     * Список публикаций пользователя
     *
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
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
    public function index(User $user)
    {
        return PostResource::collection(Post::where(['user_id' => $user->id])->paginate(10));
    }

    /**
     * Создание публикации
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
    public function store()
    {
        list($valid, $validationData) = $this->service->validateJsonRequest(request());

        if (!$valid) {
            return response()->json($validationData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        Post::create($validationData);

        $data = $this->service->createdResponseData();
        return response()->json($data)->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Удаление публикации
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
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
    public function destroy(Post $post)
    {
        // @todo использовать политики Laravel для проверки что публикация принадлежит текущему пользователю
        $user = request()->user();

        if ($user->id !== $post->user->id) {
            $data = $this->service->getAccessDeniedResponseData();
            return response()->json($data)->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $post->delete();

        $data = $this->service->deletedResponseData();
        return response()->json($data)->setStatusCode(Response::HTTP_OK);
    }
}
