<?php

namespace App\Listeners;

use App\Events\DemandeResidenceAcceptee;
use App\Services\Residents\SubServices\AbonnementCreationService;
use App\Services\Residents\SubServices\ResidentActivationService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour activer le statut résident et créer l'abonnement initial de 12 mois.
 */
class ActiverResidentEtAbonnement
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(
        protected ResidentActivationService $activationService,
        protected AbonnementCreationService $abonnementService
    ) {}

    /**
     * Traiter l'événement.
     */
    public function handle(DemandeResidenceAcceptee $event): void
    {
        $demande = $event->demande;
        $user = $demande->user;

        if ($user) {
            // Activer le statut résident
            $resident = $this->activationService->activate($user);

            // Créer l'abonnement initial (tarif par défaut de 5000 FCFA pour 12 mois)
            $this->abonnementService->create($resident, 5000.0, 12);

            Log::info("ActiverResidentEtAbonnement : Le statut résident et l'abonnement de 12 mois ont été activés avec succès pour l'utilisateur ID {$user->id} (Demande ID: {$demande->id}).");
        }
    }
}
