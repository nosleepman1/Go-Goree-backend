<?php

declare(strict_types=1);

namespace App\Services\Paiements\PayDunya;

use App\Services\Paiements\PayDunya\Data\InvoiceResult;
use App\Services\Paiements\PayDunya\Data\PaymentIntent;
use App\Services\Paiements\PayDunya\Enums\PayDunyaPaymentStatus;

/**
 * Contrat unique pour interagir avec PayDunya, quelle que soit l'implémentation
 * (réseau réel ou simulation). Le binding vers l'une ou l'autre est décidé par
 * la configuration (config/paydunya.php -> driver) dans PayDunyaServiceProvider.
 */
interface PayDunyaClientInterface
{
    /**
     * Créer une facture de paiement (checkout invoice) et obtenir l'URL de
     * redirection vers la page de paiement PayDunya.
     */
    public function createInvoice(PaymentIntent $intent): InvoiceResult;

    /**
     * Confirmer le statut d'un paiement auprès de PayDunya à partir de son jeton.
     * C'est la source de vérité serveur-à-serveur utilisée par le webhook.
     */
    public function confirmInvoice(string $token): PayDunyaPaymentStatus;
}
