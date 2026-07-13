<?php

namespace App\Listeners;

use App\Enums\CanalEnum;
use App\Enums\NotificationEnum;
use App\Events\AbonnementExpireBientot;
use App\Services\Notifications\NotificationDispatchService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour notifier les résidents que leur abonnement va bientôt expirer.
 */
class NotifierRenouvellementAbonnement
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected NotificationDispatchService $notifier) {}

    /**
     * Traiter l'événement.
     */
    public function handle(AbonnementExpireBientot $event): void
    {
        $abonnement = $event->abonnement;
        $resident = $abonnement->resident;
        $user = $resident ? $resident->user : null;

        if ($user && $abonnement->date_fin) {
            $message = 'Votre abonnement résident arrive à expiration le '.$abonnement->date_fin->format('d/m/Y').'. Pensez à le renouveler.';

            // Notification in-app
            $this->notifier->dispatch(
                $user,
                NotificationEnum::ALERTE,
                CanalEnum::IN_APP,
                $message
            );

            // Notification E-mail
            $this->notifier->dispatch(
                $user,
                NotificationEnum::ALERTE,
                CanalEnum::MAIL,
                $message
            );

            Log::info("NotifierRenouvellementAbonnement : Notifications envoyées à l'utilisateur ID {$user->id} pour son abonnement ID {$abonnement->id} expirant bientôt.");
        }
    }
}
