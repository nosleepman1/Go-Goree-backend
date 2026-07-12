<?php

namespace App\Jobs;

use App\Events\AbonnementExpireBientot;
use App\Models\Abonnement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour vérifier les abonnements de résident approchant de leur expiration (dans 3 jours)
 * et déclencher les événements de notification de renouvellement.
 */
class CheckExpiringAbonnementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Exécuter le job.
     */
    public function handle(): void
    {
        $threeDaysFromNow = now()->addDays(3);
        $startDate = $threeDaysFromNow->copy()->startOfDay();
        $endDate = $threeDaysFromNow->copy()->endOfDay();

        // Récupérer les abonnements actifs expirant dans exactement 3 jours
        $abonnements = Abonnement::whereBetween('date_fin', [$startDate, $endDate])->get();

        foreach ($abonnements as $abonnement) {
            event(new AbonnementExpireBientot($abonnement));
        }

        if ($abonnements->isNotEmpty()) {
            Log::info('CheckExpiringAbonnementsJob : '.$abonnements->count().' événement(s) AbonnementExpireBientot déclenché(s).');
        }
    }
}
