<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // Pharmacies de garde : seulement si une tâche cron « php artisan schedule:run » existe sur le serveur.
        // Sans cron, l'import se fait quand même automatiquement au fil des visites (toutes les 6 h au plus).
        // Rien tant que l'import automatique depuis Drive n'est pas activé (import manuel pour l'instant).
        $schedule->command('gardes:import')->everySixHours()->withoutOverlapping()
            ->when(fn() => \App\Services\GardesImport::autoActif());
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
