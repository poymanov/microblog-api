<?php

use App\User;
use App\UserSubscribe;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(UserSubscribe::class, function (Faker $faker) {
    return [
        'subscriber_id' => function () {
            return factory(User::class)->create()->id;
        },
        'publisher_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
