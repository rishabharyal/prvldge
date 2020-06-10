<?php
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'username' => uniqid('user_', true),
        'password' => app('hash')->make('password'),
        'country' => 'NP',
        'phone_number' => $faker->phoneNumber,
        'birthday' => $faker->date,
        'gender' => 'm'
    ];
});
$factory->define(\App\Memory::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        'caption' => $faker->sentence,
        'type' => $faker->sentence,
        'visibility' => $faker->numberBetween(0, 1),
        'memory_at' => $faker->date('Y-m-d')
    ];
});
