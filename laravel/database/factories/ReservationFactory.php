<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Reservation;
use Faker\Generator as Faker;

$factory->define(Reservation::class, function (Faker $faker) {
    return [
        'user_id'   => $faker->randomNumber(2),
        'lesson_id' => $faker->randomNumber(2),
    ];
});
