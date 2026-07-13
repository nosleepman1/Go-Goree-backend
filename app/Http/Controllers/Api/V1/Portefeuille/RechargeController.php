<?php

namespace App\Http\Controllers\Api\V1\Portefeuille;

use App\Enums\ModePayementEnum;
use App\Enums\StatutPayementEnum;
use App\Enums\TypeTransactionPayDunyaEnum;
use App\Events\PaiementInitie;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Portefeuille\InitierRechargeRequest;
use App\Models\Payement;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Contrôleur pour gérer le rechargement de portefeuille.
 */
class RechargeController extends Controller
{
    /**
     * Initier une transaction de recharge pour le portefeuille de l'utilisateur.
     */
    public function store(InitierRechargeRequest $request)
    {
        $user = $request->user();
        $mode = $request->payment_mode ? ModePayementEnum::from($request->payment_mode) : ModePayementEnum::PAYDUNYA;

        try {
            $reference = 'RECH_'.Str::random(12);

            $payement = Payement::create([
                'reference' => $reference,
                'montant' => (float) $request->montant,
                'statut' => StatutPayementEnum::EN_COURS,
                'mode' => $mode,
                'type_transaction' => TypeTransactionPayDunyaEnum::RECHARGE_PORTEFEUILLE,
                'paydunya_token' => 'tok_'.Str::random(20),
                'user_id' => $user->id,
            ]);

            event(new PaiementInitie($payement));

            return response()->json([
                'message' => 'Recharge initiée avec succès.',
                'payement' => $payement,
                'redirect_url' => null,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
