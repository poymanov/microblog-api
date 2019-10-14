<?php

namespace App\Dto\models;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class PostDto implements Arrayable
{
    /** @var int Идентификатор публикации */
    private $id;

    /** @var string Текст публикации */
    private $text;

    /** @var int Идентификатор автора публикации */
    private $userId;

    /** @var Carbon Дата создания публикации */
    private $createdAt;

    /** @var Carbon Дата последнего обновления публикации */
    private $updatedAt;

    /**
     * PostDto constructor.
     * @param int $id
     * @param string $text
     * @param int $userId
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(int $id, string $text, int $userId, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->text = $text;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
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
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'text' => $this->getText(),
            'user_id' => $this->getUserId(),
            'created_at' => $this->getCreatedAt()->timestamp,
            'updated_at' => $this->getUpdatedAt()->timestamp,
        ];
    }
}
