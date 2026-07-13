<?php

namespace App\Listeners;

use App\Events\PaiementInitie;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour enregistrer/journaliser chaque tentative d'initialisation de paiement.
 */
class EnregistrerTentativePaiement
{
    /**
     * Traiter l'événement.
     */
    public function handle(PaiementInitie $event): void
    {
        $payement = $event->payement;

        if (! $payement) {
            return;
        }

        Log::info("EnregistrerTentativePaiement : Paiement ID {$payement->id} initié. Montant : {$payement->montant} FCFA, Mode : {$payement->mode->value}, Statut : {$payement->statut->value}");
    }
}
