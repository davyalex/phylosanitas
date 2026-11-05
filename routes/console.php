<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pharmacies de garde : import depuis la feuille Google Sheets (voir DOCUMENTATION.md)
Artisan::command('gardes:import', function () {
    $r = \App\Services\GardesImport::importer('commande');
    $r['ok'] ? $this->info($r['message']) : $this->error($r['message']);
    return $r['ok'] ? 0 : 1;
})->purpose('Importe les pharmacies de garde depuis Google Drive (nouveau fichier uniquement)');
