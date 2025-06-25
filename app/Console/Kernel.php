<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
    $schedule->command('promociones:limpiar')
        ->daily() // Se ejecuta a medianoche
        ->timezone('America/Mexico_City') // Ajusta según tu zona horaria
        ->appendOutputTo(storage_path('logs/promociones.log')); // Opcional: registrar salida       
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}
