<?php

/** @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\Gause\ImageableLaravel\Models\Image::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'short_description' => $faker->sentence,
        'description' => $faker->sentence,
        'file_name' => $faker->word,
        'file_extension' => $faker->fileExtension,
        'file_size' => 0,
        'original_file_name' => null,
        'position' => 1,
        'model_id' => null,
        'model_type' => null,
        'created_by' => null,
    ];
});
