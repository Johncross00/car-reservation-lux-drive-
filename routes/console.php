<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planifier la mise à jour automatique des statuts des réservations toutes les heures
Schedule::command('reservations:update-status')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
