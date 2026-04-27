<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Kelola kost dengan rapi, satu kamar demi satu kamar.');
})->purpose('Display an inspiring quote');
