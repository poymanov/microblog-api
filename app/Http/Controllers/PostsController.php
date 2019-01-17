<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Services\PostsService;
use Illuminate\Http\Response;

class PostsController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new PostsService();
        $this->middleware('auth:api')->only(['store', 'destroy']);
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
