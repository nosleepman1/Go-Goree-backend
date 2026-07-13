<?php

declare(strict_types=1);

namespace App\Services\Paiements;

use App\Models\Payement;
use App\Services\Paiements\PayDunya\Data\InvoiceResult;
use App\Services\Paiements\PayDunya\Data\PaymentIntent;
use App\Services\Paiements\PayDunya\Enums\PayDunyaPaymentStatus;
use App\Services\Paiements\PayDunya\Exceptions\PayDunyaException;
use App\Services\Paiements\PayDunya\PayDunyaClientInterface;
use Illuminate\Support\Facades\Log;

/**
 * Point d'entrée métier pour PayDunya : crée la facture pour un Payement donné,
 * mémorise le jeton renvoyé, et expose la confirmation de statut.
 *
 * Ne gère PAS la mutation du solde ni des statuts métier : cela reste piloté par
 * les événements (PaiementAccepte / PaiementRefuse) et leurs écouteurs.
 */
class PayDunyaPaymentService
{
    public function __construct(protected PayDunyaClientInterface $client) {}

    /**
     * Initier le paiement d'un Payement existant auprès de PayDunya.
     *
     * @throws PayDunyaException si l'initiation échoue.
     */
    public function initier(Payement $payement, string $description): InvoiceResult
    {
        $intent = new PaymentIntent(
            reference: $payement->reference,
            montant: (float) $payement->montant,
            description: $description,
            customData: [
                'payement_id' => $payement->id,
                'type_transaction' => $payement->type_transaction?->value,
            ],
        );

        $result = $this->client->createInvoice($intent);

        if (! $result->success || $result->token === null) {
            throw new PayDunyaException(
                $result->message ?? 'Échec de l’initiation du paiement PayDunya.'
            );
        }

        $payement->update(['paydunya_token' => $result->token]);

        Log::info('PayDunyaPaymentService : facture créée', [
            'payement_id' => $payement->id,
            'reference' => $payement->reference,
            'token' => $result->token,
        ]);

        return $result;
    }

    /**
     * Confirmer le statut d'un paiement auprès de PayDunya (source de vérité).
     */
    public function confirmer(string $token): PayDunyaPaymentStatus
    {
        return $this->client->confirmInvoice($token);
    }
}
