<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Model\Document;

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

$factory->define(Document::class, function (Faker $faker) {
    return [
        'name'                  => $faker->name,
        'path'                  => '/document/aa.pdf',
        'signed_path'           => '/document/signed/aa.pdf',
        'is_signed'             => false,
        'width'                 => 300,
        'height'                => 600,
        'numPages'              => 1,
        'signature_position_id' => 1,
    ];
});
