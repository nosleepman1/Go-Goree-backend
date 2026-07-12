<?php

namespace App\Listeners;

use App\Events\PaiementWebhookRecu;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour consigner ou traiter les webhooks de paiement reçus de PayDunya.
 */
class TraiterWebhookPayDunya
{
    /**
     * Traiter l'événement.
     */
    public function handle(PaiementWebhookRecu $event): void
    {
        Log::info('TraiterWebhookPayDunya : Webhook de paiement reçu. Données : ', $event->payload);
    }
}
