<?php

declare(strict_types=1);

namespace App\Services\Paiements\PayDunya\Data;

/**
 * Objet-valeur décrivant l'intention de paiement transmise à PayDunya pour
 * créer une facture (checkout invoice).
 */
final readonly class PaymentIntent
{
    /**
     * @param  string  $reference  Référence interne unique du paiement.
     * @param  float  $montant  Montant à payer (dans la devise configurée).
     * @param  string  $description  Description affichée à l'utilisateur.
     * @param  array<string, scalar|null>  $customData  Données renvoyées telles quelles dans l'IPN.
     */
    public function __construct(
        public string $reference,
        public float $montant,
        public string $description,
        public array $customData = [],
    ) {}
}
