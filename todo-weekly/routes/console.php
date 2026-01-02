<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mỗi ngày 00:05 sinh task lặp cho 14 ngày tới
Schedule::command('tasks:generate-recurring --days=14')->dailyAt('00:05');