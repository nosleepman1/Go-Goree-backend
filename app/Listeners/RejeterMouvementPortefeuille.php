<?php

namespace App\Listeners;

use App\Enums\StatutMouvementEnum;
use App\Events\PaiementRefuse;
use App\Models\MouvementPortefeuille;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour rejeter les mouvements de portefeuille en attente lorsque le paiement associé est refusé.
 */
class RejeterMouvementPortefeuille
{
    /**
     * Traiter l'événement.
     */
    public function handle(PaiementRefuse $event): void
    {
        $payement = $event->payement;

        if (! $payement) {
            return;
        }

        // Mettre à REJETE tous les mouvements en attente pour cette transaction
        $updated = MouvementPortefeuille::where('payement_id', $payement->id)
            ->where('statut', StatutMouvementEnum::EN_ATTENTE)
            ->update(['statut' => StatutMouvementEnum::REJETE]);

        if ($updated > 0) {
            Log::info("RejeterMouvementPortefeuille : {$updated} mouvement(s) de portefeuille en attente associé(s) au paiement ID {$payement->id} ont été marqués comme REJETÉS.");
        }
    }
}
