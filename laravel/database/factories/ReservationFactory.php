<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Reservation;
use Faker\Generator as Faker;

$factory->define(Reservation::class, function (Faker $faker) {
    return [
        'user_id'   => function () {
            return factory(User::class)->create()->id;
        },
        'lesson_id' => function () {
            return factory(Lesson::class)->create()->id;
        }
    ];
});
