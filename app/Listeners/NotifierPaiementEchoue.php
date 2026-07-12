<?php

namespace App\Listeners;

use App\Enums\CanalEnum;
use App\Enums\NotificationEnum;
use App\Events\PaiementRefuse;
use App\Services\Notifications\NotificationDispatchService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour notifier l'utilisateur de l'échec ou du rejet de son paiement.
 */
class NotifierPaiementEchoue
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected NotificationDispatchService $notifier) {}

    /**
     * Traiter l'événement.
     */
    public function handle(PaiementRefuse $event): void
    {
        $payement = $event->payement;

        if (! $payement) {
            return;
        }

        $user = $payement->user;

        if ($user) {
            $message = "Échec du paiement : Votre transaction de {$payement->montant} FCFA via {$payement->mode->value} a échoué ou a été refusée. Référence : {$payement->reference}";

            // Notification in-app
            $this->notifier->dispatch(
                $user,
                NotificationEnum::PAYEMENT,
                CanalEnum::IN_APP,
                $message
            );

            // Notification E-mail
            $this->notifier->dispatch(
                $user,
                NotificationEnum::PAYEMENT,
                CanalEnum::MAIL,
                $message
            );

            Log::info("NotifierPaiementEchoue : Notifications envoyées à l'utilisateur ID {$user->id} pour le paiement échoué ID {$payement->id}");
        }
    }
}
