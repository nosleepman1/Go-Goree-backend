<?php

namespace App\Http\Controllers\Api\V1\Portefeuille;

use App\Enums\ModePayementEnum;
use App\Enums\StatutPayementEnum;
use App\Enums\TypeTransactionPayDunyaEnum;
use App\Events\PaiementInitie;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Portefeuille\InitierRechargeRequest;
use App\Models\Payement;
use App\Services\Paiements\PayDunya\Exceptions\PayDunyaException;
use App\Services\Paiements\PayDunyaPaymentService;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Contrôleur pour gérer le rechargement de portefeuille via PayDunya.
 */
class RechargeController extends Controller
{
    public function __construct(protected PayDunyaPaymentService $payDunya) {}

    /**
     * Initier une transaction de recharge pour le portefeuille de l'utilisateur.
     *
     * Le crédit effectif du solde n'a lieu qu'à la confirmation du paiement par
     * le webhook PayDunya (signé) — jamais directement ici.
     */
    public function store(InitierRechargeRequest $request)
    {
        $user = $request->user();
        $mode = $request->payment_mode
            ? ModePayementEnum::from($request->payment_mode)
            : ModePayementEnum::PAYDUNYA;

        $payement = Payement::create([
            'reference' => 'RECH_'.Str::random(12),
            'montant' => (float) $request->montant,
            'statut' => StatutPayementEnum::EN_COURS,
            'mode' => $mode,
            'type_transaction' => TypeTransactionPayDunyaEnum::RECHARGE_PORTEFEUILLE,
            'paydunya_token' => null,
            'user_id' => $user->id,
        ]);

        try {
            $result = $this->payDunya->initier(
                $payement,
                'Recharge portefeuille Go Gorée ('.$payement->reference.')'
            );
        } catch (PayDunyaException $e) {
            return response()->json([
                'message' => "Impossible d'initier la recharge : ".$e->getMessage(),
            ], Response::HTTP_BAD_GATEWAY);
        }

        event(new PaiementInitie($payement));

        // On n'expose JAMAIS le paydunya_token au client : uniquement l'URL de
        // paiement vers laquelle rediriger l'utilisateur.
        return response()->json([
            'message' => 'Recharge initiée avec succès.',
            'reference' => $payement->reference,
            'montant' => (float) $payement->montant,
            'statut' => $payement->statut->value,
            'redirect_url' => $result->checkoutUrl,
        ], Response::HTTP_CREATED);
    }
}
