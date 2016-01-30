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
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Project::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'verified' => rand(0, 1),
        'description' => $faker->text,
        'total_pledged' => rand(0, 10000) / 100,
        'user_id' => rand(0, 10)
    ];
});
$factory->define(App\UserPledge::class, function (Faker\Generator $faker) {
    return [
        'project_id' => rand(0,10),
        'amount' => rand(0, 10000) / 100,
        'user_id' => rand(0, 10)
    ];
});