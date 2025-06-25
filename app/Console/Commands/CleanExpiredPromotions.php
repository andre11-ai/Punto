<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Promocion;
use Carbon\Carbon;

class CleanExpiredPromotions extends Command
{
    protected $signature = 'promotions:clean';
    protected $description = 'Remove expired promotions';

    public function handle()
    {
        $today = Carbon::today();

        $expiredPromotions = Promocion::where('fecha_fin', '<', $today)->get();

        foreach ($expiredPromotions as $promotion) {
            $promotion->productos()->update(['promocion_id' => null]);

            $promotion->delete();
        }

        $this->info('Expired promotions cleaned: ' . $expiredPromotions->count());
    }
}
