<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 * @package App
 *
 * @property string $text
 * @property integer $author_id
 *
 * @property User $user
 */

/**
 * @OA\Schema(
 *     description="Публикация",
 *     title="Post",
 *     required={"id", "text", "user_id"},
 *     @OA\Property(property="id", type="int"),
 *     @OA\Property(property="text", type="string", maxLength=300),
 *     @OA\Property(property="user_id", type="int")
 * )
 */
class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
