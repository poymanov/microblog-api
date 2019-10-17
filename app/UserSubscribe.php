<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserSubscribe
 * @package App
 *
 * @property int subscriber_id
 * @property int publisher_id
 */
class UserSubscribe extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'subscriber_id', 'publisher_id'
    ];
}
