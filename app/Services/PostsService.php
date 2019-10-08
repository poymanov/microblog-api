<?php

namespace App\Services;

use App\Dto\factories\SuccessfulResponseDtoFactory;
use App\Dto\ResponseDtoInterface;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Post;
use App\Repository\PostsRepository;
use Exception;

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

    /** @var UsersService Сервис для управления пользователями */
    private $usersService;

    /**
     * PostsService constructor.
     */
    public function __construct()
    {
        $this->repository = app(PostsRepository::class);
        $this->usersService = app(UsersService::class);
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

        if (is_null($user)) {
            throw new NotFoundException();
        }

        return $this->repository->getByUserId($user->id, self::POSTS_PER_PAGE);
    }

    /**
     * Создание публикации
     *
     * @param array $data
     * @param int $userId
     * @return ResponseDtoInterface
     * @throws NotFoundException
     * @throws \App\Exceptions\ValidationException
     */
    public function createPost(array $data, int $userId): ResponseDtoInterface
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        $data['user_id'] = $user->id;

        $validationRules = [
            'text' => 'required|max:300',
            'user_id' => 'required|exists:users,id',
        ];

        $this->repository->validateData($data, $validationRules);
        $this->repository->create($data);

        return SuccessfulResponseDtoFactory::buildSuccessfulCreated();
    }

    /**
     * Удаление публикации
     *
     * @param int $postId
     * @param int $userId
     * @return ResponseDtoInterface
     * @throws AccessDeniedException
     * @throws NotFoundException
     * @throws Exception
     */
    public function deletePost(int $postId, int $userId): ResponseDtoInterface
    {
        // Получение пользователя
        $user = $this->usersService->getById($userId);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        // Получение публикации
        $post = $this->getById($postId);

        if (is_null($post)) {
            throw new NotFoundException();
        }

        // Проверка: публикация принадлежит пользователю
        if ($user->id != $post->user_id) {
            throw new AccessDeniedException();
        }

        $this->repository->delete($post->id);

        return SuccessfulResponseDtoFactory::buildSuccessfulDeleted();
    }
}
