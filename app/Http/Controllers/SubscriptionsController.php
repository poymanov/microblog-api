<?php

namespace App\Http\Controllers;

use App\Services\SubscribeService;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionsController extends Controller
{
    /** @var SubscribeService */
    private $service;

    /**
     * @OA\Tag(
     *     name="subscriptions",
     *     description="Работа с подписками",
     * )
     */
    /**
     * SubscriptionsController constructor.
     * @param SubscribeService $service
     */
    public function __construct(SubscribeService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api')->only(['subscribe', 'unsubscribe']);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{id}/subscribe",
     *     tags={"subscriptions"},
     *     summary="Подписка на пользователя",
     *     @OA\Response(response="204", description="Успешная подписка на пользователя"),
     *     @OA\Response(response="403", description="Попытка подписки неавторизованным пользователем",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Access denied"),
     *              @OA\Property(property="errors", type="array",
     *                  @OA\Items(type="string", example="You have not access permission to API.")
     *              ),
     *         ),
     *     ),
     *     @OA\Response(response="404", description="Попытка подписки на несуществующего пользователя",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found"),
     *         ),
     *     ),
     * )
     */
    /**
     * Подписка на пользователя
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\UserSubscribeHimselfException
     * @throws \App\Exceptions\ValidationException
     */
    public function subscribe(int $id)
    {
        $this->service->subscribe(request()->user()->id, $id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{id}/unsubscribe",
     *     tags={"subscriptions"},
     *     summary="Отписка от пользователя",
     *     @OA\Response(response="204", description="Успешная отписка от пользователя"),
     *     @OA\Response(response="403", description="Попытка отподписки неавторизованным пользователем",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Access denied"),
     *              @OA\Property(property="errors", type="array",
     *                  @OA\Items(type="string", example="You have not access permission to API.")
     *              ),
     *         ),
     *     ),
     *     @OA\Response(response="404", description="Попытка отподписки от несуществующего пользователя",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found")
     *         ),
     *     ),
     * )
     */
    /**
     * Отписка от пользователя
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\UserUnsubscribeFromNotSubscribedUser
     */
    public function unsubscribe(int $id)
    {
        $this->service->unsubscribe(request()->user()->id, $id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
