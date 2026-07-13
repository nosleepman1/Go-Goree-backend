<?php

namespace App\Listeners;

use App\Enums\CanalEnum;
use App\Enums\NotificationEnum;
use App\Events\DemandeResidenceSoumise;
use App\Models\User;
use App\Services\Notifications\NotificationDispatchService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour notifier les agents/administrateurs lors de la soumission d'une nouvelle demande de résidence.
 */
class NotifierAgentNouvelleDemande
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected NotificationDispatchService $notifier) {}

    /**
     * Traiter l'événement.
     */
    public function handle(DemandeResidenceSoumise $event): void
    {
        $demande = $event->demande;
        $demandeur = $demande->user;

        if (! $demandeur) {
            return;
        }

        // Récupérer tous les administrateurs
        $admins = User::whereHas('role', function ($query) {
            $query->where('nom', 'Admin');
        })->get();

        $message = "Nouvelle demande de résidence soumise par {$demandeur->prenom} {$demandeur->nom} (ID: {$demande->id}).";

        foreach ($admins as $admin) {
            $this->notifier->dispatch(
                $admin,
                NotificationEnum::ALERTE,
                CanalEnum::IN_APP,
                $message
            );
        }

        Log::info('NotifierAgentNouvelleDemande : Notification envoyée à '.$admins->count()." administrateurs pour la demande ID {$demande->id}");
    }
}
