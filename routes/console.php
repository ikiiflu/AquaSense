<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Roda sensor:simulate a cada minuto via scheduler (1 lote por execução)
Schedule::command('sensor:simulate')->everyMinute()->withoutOverlapping();
