<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('subscriptions:check-expiry')->dailyAt('08:00');

// Daily database backup
Schedule::command('backup:run --only-db')->dailyAt('02:00');

// Weekly cleanup of old backups per retention policy
Schedule::command('backup:clean')->daily()->at('03:00');

// Daily health check - verifies backups exist and aren't stale
Schedule::command('backup:monitor')->dailyAt('04:00');
