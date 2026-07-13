<?php

namespace App\Services\Billetterie\SubServices;

use App\Enums\ModePayementEnum;
use App\Enums\StatutPayementEnum;
use App\Enums\TypeTransactionPayDunyaEnum;
use App\Models\Billet;
use App\Models\Payement;
use App\Services\Paiements\PayDunya\Exceptions\PayDunyaException;
use App\Services\Paiements\PayDunyaPaymentService;
use Illuminate\Support\Str;

/**
 * Service pour l'enregistrement et l'initiation de la transaction de paiement d'un billet.
 *
 * - Mode PORTEFEUILLE : aucun passage par PayDunya (le débit est géré en amont
 *   par BilletPurchaseService), le paiement reste local.
 * - Autres modes : délégation à PayDunya pour obtenir une URL de redirection.
 */
class PaymentInitiationService
{
    public function __construct(protected PayDunyaPaymentService $payDunya) {}

    /**
     * Initier le paiement pour le billet.
     *
     * @return array{success: bool, payement: Payement, redirect_url: ?string, message?: string}
     */
    public function initiate(Billet $billet, ModePayementEnum $mode): array
    {
        $payement = Payement::create([
            'reference' => 'PAY_'.Str::random(12),
            'montant' => $billet->montant,
            'statut' => StatutPayementEnum::EN_COURS,
            'mode' => $mode,
            'type_transaction' => TypeTransactionPayDunyaEnum::ACHAT_BILLET,
            'paydunya_token' => null,
            'billet_id' => $billet->id,
            'user_id' => $billet->user_id,
        ]);

        // Paiement par portefeuille : traité localement, pas de redirection PayDunya.
        if ($mode === ModePayementEnum::PORTEFEUILLE) {
            return [
                'success' => true,
                'payement' => $payement,
                'redirect_url' => null,
            ];
        }

        try {
            $result = $this->payDunya->initier($payement, "Achat billet Go Gorée ({$payement->reference})");
        } catch (PayDunyaException $e) {
            return [
                'success' => false,
                'payement' => $payement,
                'redirect_url' => null,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'success' => true,
            'payement' => $payement->refresh(),
            'redirect_url' => $result->checkoutUrl,
        ];
    }
}
