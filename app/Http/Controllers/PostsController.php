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
    public function index(User $user)
    {
        return PostResource::collection(Post::where(['user_id' => $user->id])->paginate(10));
    }

    /**
     * Создание публикации
     *
     * @return \Illuminate\Http\JsonResponse
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
