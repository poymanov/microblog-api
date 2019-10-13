<?php

namespace App\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\ResponseDtoInterface;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Post;
use App\Repository\PostRepository;
use Exception;

/**
 * Class PostService
 * @package App\Services
 *
 * Сервис управления публикациями
 */
class PostService extends BaseService
{
    /** Количество публикаций на одной странице выдачи */
    const POSTS_PER_PAGE = 10;

    /** @var PostRepository Репозиторий для работы с публикациями*/
    private $repository;

    /** @var UserService Сервис для управления пользователями */
    private $usersService;

    /**
     * PostService constructor.
     */
    public function __construct()
    {
        $this->repository = app(PostRepository::class);
        $this->usersService = app(UserService::class);
    }

    /**
     * Получение публикации по id
     *
     * @param int $id
     * @return Post|null
     */
    public function getById(int $id): ?Post
    {
        return $this->repository->getById($id);
    }

    /**
     * Получение публикаций пользователя
     *
     * @param int $userId
     * @return mixed
     * @throws NotFoundException
     */
    public function getUserPosts(int $userId)
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        $this->throwExceptionIfNull($user);

        return $this->repository->getByUserId($user->id, self::POSTS_PER_PAGE);
    }

    /**
     * Создание публикации
     *
     * @param array $data
     * @param int $userId
     * @return ResponseDtoInterface
     * @throws \App\Exceptions\ValidationException
     */
    public function createPost(array $data, int $userId): ResponseDtoInterface
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        $this->throwExceptionIfNull($user);

        $data['user_id'] = $user->id;

        $this->repository->validateData($data, $this->repository->getCreatingValidationRules());
        $this->repository->create($data);

        return SuccessfulResponseDtoFactory::buildSuccessfulCreated();
    }

    /**
     *  Удаление публикации
     *
     * @param int $postId
     * @param int $userId
     * @return ResponseDtoInterface
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function deletePost(int $postId, int $userId): ResponseDtoInterface
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        $this->throwExceptionIfNull($user);

        // Получение публикации
        $post = $this->getById($postId);

        $this->throwExceptionIfNull($post);

        // Проверка: публикация принадлежит пользователю
        if ($user->id != $post->user_id) {
            throw new AccessDeniedException();
        }

        $this->repository->delete($post->id);

        return SuccessfulResponseDtoFactory::buildSuccessfulDeleted();
    }
}
