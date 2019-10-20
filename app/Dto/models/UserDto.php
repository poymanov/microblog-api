<?php

namespace App\Dto\models;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class UserDto
 * @package App\Dto\models
 *
 * Объект пользователя
 */
class UserDto implements Arrayable
{
    /** @var int Идентификатор пользователя */
    private $id;

    /** @var string Имя пользователя */
    private $name;

    /** @var string Email пользователя */
    private $email;

    /** @var Carbon Дата создания пользователя */
    private $createdAt;

    /** @var Carbon Дата последнего изменения пользователя */
    private $updatedAt;

    /** @var int Количество подписок */
    private $subscriptionsCount;

    /** @var int Количество подписчиков*/
    private $subscribersCount;

    /**
     * UserDto constructor.
     * @param int $id
     * @param string $name
     * @param string $email
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     * @param int $subscriptionsCount
     * @param int $subscribersCount
     */
    public function __construct(
        int $id,
        string $name,
        string $email,
        Carbon $createdAt,
        Carbon $updatedAt,
        int $subscriptionsCount,
        int $subscribersCount
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->subscriptionsCount = $subscriptionsCount;
        $this->subscribersCount = $subscribersCount;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getSubscriptionsCount(): int
    {
        return $this->subscriptionsCount;
    }

    /**
     * @return int
     */
    public function getSubscribersCount(): int
    {
        return $this->subscribersCount;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'created_at' => $this->getCreatedAt()->timestamp,
            'updated_at' => $this->getUpdatedAt()->timestamp,
            'subscriptions_count' => $this->getSubscriptionsCount(),
            'subscribers_count' => $this->getSubscriptionsCount(),
        ];
    }
}
