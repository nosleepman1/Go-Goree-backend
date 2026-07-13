<?php

namespace App\Listeners;

use App\Enums\NiveauAlerteFraudeEnum;
use App\Enums\StatutPayementEnum;
use App\Events\FraudeDetectee;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour bloquer ou suspendre les transactions suspectes détectées comme fraudes critiques.
 */
class BloquerTransactionSuspecte
{
    /**
     * Traiter l'événement.
     */
    public function handle(FraudeDetectee $event): void
    {
        $alerte = $event->alerte;

        if (! $alerte) {
            return;
        }

        $payement = $alerte->payement;

        // Si l'alerte est critique, on marque le paiement associé comme suspect
        if ($payement && $alerte->niveau === NiveauAlerteFraudeEnum::CRITIQUE) {
            $payement->update([
                'statut' => StatutPayementEnum::SUSPECT,
            ]);

            Log::warning("BloquerTransactionSuspecte : Transaction ID {$payement->id} marquée comme SUSPECTE suite à une alerte de fraude critique (Alerte ID: {$alerte->id}).");
        }
    }
}
