<?php

namespace App\Services;

use App\Dto\models\PostDto;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
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
     * @return PostDto
     * @throws NotFoundException
     */
    public function getById(int $id): PostDto
    {
        return $this->repository->getById($id);
    }

    /**
     * Получение публикаций пользователя
     *
     * @param int $userId
     * @return PostDto[]
     */
    public function getUserPosts(int $userId): array
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        $this->throwExceptionIfNull($user);

        return $this->repository->getByUserId($user->id, self::POSTS_PER_PAGE);
    }

    /**
     * Получение публикаций пользователя в виде распакованного массива
     *
     * @param int $userId
     * @return PostDto[]
     */
    public function getUserPostsExtracted(int $userId): array
    {
        $data = [];

        // Получение публикаций в виде массива DTO
        $dtos = $this->getUserPosts($userId);

        foreach ($dtos as $dto) {
            /** @var $dto PostDto */
            $data[] = $dto->toArray();
        }

        return $data;
    }

    /**
     * Создание публикации
     *
     * @param array $data
     * @param int $userId
     * @return PostDto
     * @throws NotFoundException
     * @throws \App\Exceptions\ValidationException
     */
    public function createPost(array $data, int $userId): PostDto
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        $this->throwExceptionIfNull($user);

        $data['user_id'] = $user->id;

        $this->repository->validateData($data, $this->repository->getCreatingValidationRules());
        $postId = $this->repository->create($data);

        return $this->repository->getById($postId);
    }

    /**
     * Удаление публикации
     *
     * @param int $postId
     * @param int $userId
     * @throws AccessDeniedException
     * @throws NotFoundException
     * @throws Exception
     */
    public function deletePost(int $postId, int $userId): void
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        $this->throwExceptionIfNull($user);

        // Получение публикации
        $post = $this->getById($postId);

        // Проверка: публикация принадлежит пользователю
        if ($user->id != $post->getUserId()) {
            throw new AccessDeniedException();
        }

        $this->repository->delete($post->getId());
    }
}
