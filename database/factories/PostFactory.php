<?php

use Faker\Generator as Faker;
use App\User;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'text' => $faker->realText(),
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
