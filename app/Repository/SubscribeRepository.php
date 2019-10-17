<?php

namespace App\Repository;

use App\UserSubscribe;

/**
 * Репозиторий для управления подписками
 *
 * Class SubscribeRepository
 * @package App\Repository
 */
class SubscribeRepository extends AbstractRepository
{
    /**
     * Подписка на пользователя
     *
     * @param int $subscriberId
     * @param int $publisherId
     */
    public function subscribe(int $subscriberId, int $publisherId): void
    {
        UserSubscribe::create([
            'subscriber_id' => $subscriberId,
            'publisher_id' => $publisherId,
        ]);
    }

    /**
     * Отписка от пользователя
     *
     * @param int $subscriberId
     * @param int $publisherId
     */
    public function unsubscribe(int $subscriberId, int $publisherId): void
    {
        UserSubscribe::where([
            'subscriber_id' => $subscriberId, 'publisher_id' => $publisherId
        ])->delete();
    }

    /**
     * Проверка: подписан ли один пользователь на другого
     *
     * @param int $subscriberId
     * @param int $publisherId
     * @return bool
     */
    public function isExist(int $subscriberId, int $publisherId): bool
    {
        return UserSubscribe::where([
            'subscriber_id' => $subscriberId, 'publisher_id' => $publisherId
        ])->exists();
    }

    /**
     * Правила валидации для подписки
     *
     * @return array
     */
    public function getSubscribeValidationRules(): array
    {
        return [
            'subscriber_id' => ['required', 'integer' ,'exists:users,id'],
            'publisher_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
