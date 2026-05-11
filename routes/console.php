<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('media:relink', function () {
    $this->info('Relinking attraction media from storage files...');

    Artisan::call('db:seed', [
        '--class' => \Database\Seeders\AttractionMediaSeeder::class,
        '--force' => true,
    ]);

    $this->line(trim(Artisan::output()));
    $this->info('Attraction media relink completed.');
})->purpose('Relink attraction images and videos from storage without full reseed');
