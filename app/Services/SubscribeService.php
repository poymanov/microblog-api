<?php

namespace App\Services;

use App\Exceptions\UserSubscribeHimselfException;
use App\Exceptions\UserUnsubscribeFromNotSubscribedUser;
use App\Repository\SubscribeRepository;

/**
 * Сервис управления подписками
 */
class SubscribeService
{
    /** @var UserService */
    private $userService;

    /** @var SubscribeRepository */
    private $repository;

    /**
     * SubscribeService constructor.
     * @param UserService $userService
     * @param SubscribeRepository $repository
     */
    public function __construct(UserService $userService, SubscribeRepository $repository)
    {
        $this->userService = $userService;
        $this->repository = $repository;
    }

    /**
     * Подписка на пользователя
     *
     * @param int $subscriberId
     * @param int $publisherId
     * @throws \App\Exceptions\NotFoundException
     * @throws UserSubscribeHimselfException
     * @throws \App\Exceptions\ValidationException
     */
    public function subscribe(int $subscriberId, int $publisherId)
    {
        $subscriber = $this->userService->getById($subscriberId);
        $publisher = $this->userService->getById($publisherId);

        if ($subscriber->getId() == $publisher->getId()) {
            throw new UserSubscribeHimselfException();
        }

        $data = [
            'subscriber_id' => $subscriber->getId(),
            'publisher_id' => $publisher->getId(),
        ];

        $this->repository->validateData($data, $this->repository->getSubscribeValidationRules());

        $this->repository->subscribe($subscriber->getId(), $publisher->getId());
    }

    /**
     * Отписка от пользователя
     *
     * @param int $subscriberId
     * @param int $publisherId
     * @throws \App\Exceptions\NotFoundException
     * @throws UserUnsubscribeFromNotSubscribedUser
     */
    public function unsubscribe(int $subscriberId, int $publisherId)
    {
        $subscriber = $this->userService->getById($subscriberId);
        $publisher = $this->userService->getById($publisherId);

        if (!$this->repository->isExist($subscriber->getId(), $publisher->getId())) {
            throw new UserUnsubscribeFromNotSubscribedUser();
        }

        $this->repository->unsubscribe($subscriberId, $publisherId);
    }
}
