<?php

namespace App\Listeners;

use App\Enums\StatutBilletEnum;
use App\Events\BilletAchete;
use App\Events\PaiementAccepte;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour confirmer le paiement d'un billet lorsque le paiement est validé.
 */
class ConfirmerBilletPaye
{
    /**
     * Traiter l'événement.
     */
    public function handle(PaiementAccepte $event): void
    {
        $payement = $event->payement;

        if (! $payement) {
            return;
        }

        $billet = $payement->billet;

        if ($billet) {
            // Mettre à jour le statut du billet à PAYE
            $billet->update([
                'statut' => StatutBilletEnum::PAYE,
            ]);

            Log::info("ConfirmerBilletPaye : Le billet ID {$billet->id} a été marqué comme PAYE suite à l'acceptation du paiement ID {$payement->id}.");

            // Déclencher l'événement BilletAchete pour envoi de reçu / génération QR
            event(new BilletAchete($billet));
        }
    }
}
