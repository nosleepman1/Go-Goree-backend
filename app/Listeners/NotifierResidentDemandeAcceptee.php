<?php

namespace App\Listeners;

use App\Enums\CanalEnum;
use App\Enums\NotificationEnum;
use App\Events\DemandeResidenceAcceptee;
use App\Services\Notifications\NotificationDispatchService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour notifier le résident lorsque sa demande de statut résident a été acceptée.
 */
class NotifierResidentDemandeAcceptee
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected NotificationDispatchService $notifier) {}

    /**
     * Traiter l'événement.
     */
    public function handle(DemandeResidenceAcceptee $event): void
    {
        $demande = $event->demande;
        $user = $demande->user;

        if ($user) {
            $message = 'Félicitations ! Votre demande de statut résident a été acceptée.';

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

            Log::info("NotifierResidentDemandeAcceptee : Notifications envoyées à l'utilisateur ID {$user->id} pour l'acceptation de sa demande.");
        }
    }
}
