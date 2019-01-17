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
