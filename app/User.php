<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @OA\Schema(
 *     description="Пользователь",
 *     title="User",
 *     required={"id", "name", "email", "password"},
 *     @OA\Property(property="id", type="int"),
 *     @OA\Property(property="name", type="string", maxLength=255),
 *     @OA\Property(property="email", type="string", maxLength=255),
 *     @OA\Property(property="password", type="string", maxLength=255),
 *     @OA\Property(property="remember_token", type="string", maxLength=100)
 * )
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Подписки
     *
     * @return HasManyThrough
     */
    public function subscriptions(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            UserSubscribe::class,
            'subscriber_id',
            'id',
            'id',
            'publisher_id'
        );
    }

    /**
     * Подписчики
     *
     * @return HasManyThrough
     */
    public function subscribers(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            UserSubscribe::class,
            'publisher_id',
            'id',
            'id',
            'subscriber_id'
        );
    }

    /**
     * Количество подписок
     *
     * @return int
     */
    public function getSubscriptionsCountAttribute(): int
    {
        return $this->subscriptions()->count();
    }

    /**
     * Количество подписчиков
     *
     * @return int
     */
    public function getSubscribersCountAttribute(): int
    {
        return $this->subscribers()->count();
    }
}
