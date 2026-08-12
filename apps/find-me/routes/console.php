<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('find-me:health', function () {
    $this->info('ok');
})->purpose('Verify that the standalone Find Me application can boot');
