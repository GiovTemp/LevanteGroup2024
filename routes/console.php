<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();


Schedule::command('articles:reindex-published')->daily();
Schedule::command('sitemap:generate')->daily();
Schedule::command('filament:clear-temp')->dailyAt('00:00')->withoutOverlapping();

Schedule::command('app:generate-article')->dailyAt('04:00')->withoutOverlapping();