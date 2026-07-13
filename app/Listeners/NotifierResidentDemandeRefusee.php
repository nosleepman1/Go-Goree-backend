<?php

namespace App\Listeners;

use App\Enums\CanalEnum;
use App\Enums\NotificationEnum;
use App\Events\DemandeResidenceRefusee;
use App\Services\Notifications\NotificationDispatchService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour notifier le résident lorsque sa demande de statut résident a été refusée.
 */
class NotifierResidentDemandeRefusee
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected NotificationDispatchService $notifier) {}

    /**
     * Traiter l'événement.
     */
    public function handle(DemandeResidenceRefusee $event): void
    {
        $demande = $event->demande;
        $user = $demande->user;

        if ($user) {
            $message = 'Désolé, votre demande de statut résident a été refusée. Motif : '.($demande->motif_refus ?? 'Non spécifié');

            // Envoi d'une notification in-app
            $this->notifier->dispatch(
                $user,
                NotificationEnum::ALERTE,
                CanalEnum::IN_APP,
                $message
            );

            // Envoi d'un mail
            $this->notifier->dispatch(
                $user,
                NotificationEnum::ALERTE,
                CanalEnum::MAIL,
                $message
            );

            Log::info("NotifierResidentDemandeRefusee : Notifications envoyées à l'utilisateur ID {$user->id} pour le refus de sa demande.");
        }
    }
}
