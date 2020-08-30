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
        'password' => app('hash')->make('12345678'),
        'country' => 'NP',
        'phone_number' => $faker->phoneNumber,
        'birthday' => $faker->date,
        'gender' => 'm',
        'is_phone_verified' => 1
    ];
});

$factory->define(\App\Memory::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'caption' => $faker->sentence(2),
        'visibility' => $faker->numberBetween(0, 1),
    ];
});

$factory->define(\App\MemoryReplySuggestion::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(\App\User::class)->create()->id,
        'title' => $faker->sentence,
        'emoji' => 'smile',
        'keywords' => implode(',', $faker->words()),
        'metadata' => json_encode([
            "Peter" => 35,
            "Ben" => 37,
            "Joe" => 43
        ])
    ];
});

$factory->define(App\MemoryReply::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(\App\User::class)->create()->id,
        'memory_id' => factory(\App\Memory::class)->create()->id,
        'type' => $faker->word(),
        'memory_reply_suggestion_id'=> factory(\App\Reply::class)->create()->id,
        'comment'=> $faker->paragraph()
    ];
});
