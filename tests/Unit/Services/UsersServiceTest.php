<?php

namespace Tests\Unit\Services;

use App\Services\UsersService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var UsersService Тестируемый сервис */
    private $service;

    /**
     * PostsServiceTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(UsersService::class);
    }

    /**
     * Получение пользователя по id
     *
     * @test
     */
    public function get_user_by_id()
    {
        $expected = factory(User::class)->create();
        $actual = $this->service->getById($expected->id);

        $this->assertEquals($expected->id, $actual->id);
    }

    /**
     * Получение неизвестного пользователя
     *
     * @test
     */
    public function get_not_existed_user()
    {
        $actual = $this->service->getById(999);

        $this->assertNull($actual);
    }
}
