<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Promocion;
use Carbon\Carbon;

class EliminarPromocionesExpiradas extends Command
{
    protected $signature = 'promociones:limpiar';
    protected $description = 'Elimina automáticamente las promociones expiradas y sus relaciones';

    public function handle()
    {
        $fechaActual = Carbon::now();

        $promocionesExpiradas = Promocion::where('fecha_fin', '<', $fechaActual)->get();

        $count = 0;

        foreach ($promocionesExpiradas as $promocion) {
            \DB::table('productos')
                ->where('promocion_id', $promocion->id)
                ->update(['promocion_id' => null]);

            $promocion->delete();
            $count++;
        }

        $this->info("Se eliminaron {$count} promociones expiradas y sus relaciones.");
        return 0;
    }
}
