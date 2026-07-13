<?php

namespace App\Listeners;

use App\Enums\CanalEnum;
use App\Enums\NotificationEnum;
use App\Events\PaiementAccepte;
use App\Services\Notifications\NotificationDispatchService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour notifier l'utilisateur de la confirmation de son paiement.
 */
class NotifierPaiementReussi
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected NotificationDispatchService $notifier) {}

    /**
     * Traiter l'événement.
     */
    public function handle(PaiementAccepte $event): void
    {
        $payement = $event->payement;

        if (! $payement) {
            return;
        }

        $user = $payement->user;

        if ($user) {
            $message = "Paiement réussi ! Votre transaction de {$payement->montant} FCFA via {$payement->mode->value} a été validée avec succès. Référence : {$payement->reference}";

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

            Log::info("NotifierPaiementReussi : Notifications envoyées à l'utilisateur ID {$user->id} pour le paiement réussi ID {$payement->id}");
        }
    }
}
