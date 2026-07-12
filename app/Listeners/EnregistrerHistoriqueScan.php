<?php

namespace App\Listeners;

use App\Events\BilletScanne;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour consigner l'historique et les détails de chaque scan de billet.
 */
class EnregistrerHistoriqueScan
{
    /**
     * Traiter l'événement.
     */
    public function handle(BilletScanne $event): void
    {
        $scan = $event->scan;
        $billet = $scan->billet;

        if (! $scan || ! $billet) {
            return;
        }

        Log::info("EnregistrerHistoriqueScan : Billet ID {$billet->id} scanné. Résultat : {$scan->resultat->value} (Scan ID: {$scan->id}, Date: {$scan->created_at}).");
    }
}
