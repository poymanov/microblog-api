<?php

namespace App\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\ResponseDtoInterface;
use App\Dto\ValidationDto;
use App\Repository\PostsRepository;

/**
 * Class PostsService
 * @package App\Services
 *
 * Сервис управления публикациями
 */
class PostsService extends BaseService
{
    /** Количество публикаций на одной странице выдачи */
    const POSTS_PER_PAGE = 10;

    /** @var PostsRepository Репозиторий для работы с публикациями*/
    private $repository;

    /**
     * PostsService constructor.
     */
    public function __construct()
    {
        $this->repository = app(PostsRepository::class);
    }

    /**
     * Ответ "Успешное создание"
     *
     * @return ResponseDtoInterface
     */
    public function createdResponseData(): ResponseDtoInterface
    {
        return $this->createdResponseDataBase();
    }

    /**
     * Получение публикаций пользователя
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserPosts(int $userId)
    {
        return $this->repository->getByUserId($userId, self::POSTS_PER_PAGE);
    }

    /**
     * @param array $data
     * @param int $userId
     * @return ValidationDto
     * @throws \App\Exceptions\ValidationException
     */
    public function createPost(array $data, int $userId): ResponseDtoInterface
    {
        $data['user_id'] = $userId;

        $validationRules = [
            'text' => 'required|max:300',
            'user_id' => 'required|exists:users,id',
        ];

        $this->validateData($data, $validationRules);

        $this->repository->create($data);

        return SuccessfulResponseDtoFactory::buildSuccessfulCreated();
    }
}
