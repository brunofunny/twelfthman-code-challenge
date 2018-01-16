<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Image::class, function (Faker $faker) {
    return [
        'file_original_name' => "$faker->name.$faker->fileExtension",
        'file_system_name' => "$faker->md5.$faker->fileExtension",
        'file_extension' => $faker->fileExtension,
        'caption' => $faker->catchPhrase,
        'deleted' => $faker->boolean($chanceOfGettingTrue = 50)
    ];

});
