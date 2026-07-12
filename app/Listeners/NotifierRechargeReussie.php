<?php

namespace App\Listeners;

use App\Enums\CanalEnum;
use App\Enums\NotificationEnum;
use App\Events\PortefeuilleRecharge;
use App\Services\Notifications\NotificationDispatchService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour notifier l'utilisateur lors du rechargement de son portefeuille.
 */
class NotifierRechargeReussie
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected NotificationDispatchService $notifier) {}

    /**
     * Traiter l'événement.
     */
    public function handle(PortefeuilleRecharge $event): void
    {
        $portefeuille = $event->portefeuille;
        $user = $portefeuille->user;

        if ($user) {
            $message = "Votre portefeuille a été rechargé de {$event->montant} FCFA. Nouveau solde : {$portefeuille->solde} FCFA.";

            // Notification in-app
            $this->notifier->dispatch(
                $user,
                NotificationEnum::PAYEMENT,
                CanalEnum::IN_APP,
                $message
            );

            // Notification SMS
            $this->notifier->dispatch(
                $user,
                NotificationEnum::PAYEMENT,
                CanalEnum::SMS,
                $message
            );

            Log::info("NotifierRechargeReussie : Notifications envoyées à l'utilisateur ID {$user->id} pour le rechargement de {$event->montant} FCFA.");
        }
    }
}
