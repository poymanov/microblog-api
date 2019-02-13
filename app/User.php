<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
}
