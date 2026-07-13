<?php

namespace App\Listeners;

use App\Enums\TypeTransactionPayDunyaEnum;
use App\Events\PaiementAccepte;
use App\Services\Portefeuille\PortefeuilleService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour créditer le portefeuille d'un utilisateur après validation du paiement de recharge.
 */
class CrediterPortefeuille
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected PortefeuilleService $portefeuilleService) {}

    /**
     * Traiter l'événement.
     */
    public function handle(PaiementAccepte $event): void
    {
        $payement = $event->payement;

        if (! $payement) {
            return;
        }

        // Si la transaction concerne le rechargement de portefeuille
        if ($payement->type_transaction === TypeTransactionPayDunyaEnum::RECHARGE_PORTEFEUILLE) {
            $this->portefeuilleService->recharger(
                $payement->user_id,
                (float) $payement->montant,
                $payement->id
            );

            Log::info("CrediterPortefeuille : Le portefeuille de l'utilisateur ID {$payement->user_id} a été rechargé de {$payement->montant} FCFA suite au paiement ID {$payement->id}.");
        }
    }
}
