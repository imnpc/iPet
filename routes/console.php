<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:send-vaccine-reminders --days=7')->dailyAt('09:00');
Schedule::command('app:send-vaccine-reminders --days=1')->dailyAt('18:00');
