<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Enums\StatutPayementEnum;
use App\Events\PaiementAccepte;
use App\Events\PaiementRefuse;
use App\Events\PaiementWebhookRecu;
use App\Http\Controllers\Controller;
use App\Models\Payement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur pour gérer les webhooks de notification envoyés par PayDunya.
 */
class PayDunyaWebhookController extends Controller
{
    /**
     * Traiter le webhook de notification de paiement.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('PayDunyaWebhookController : Reçu webhook', $payload);

        // Déclencher l'événement du webhook reçu
        event(new PaiementWebhookRecu($payload));

        // Récupérer le jeton et le statut
        $token = $payload['token'] ?? $payload['data']['token'] ?? null;
        $status = $payload['status'] ?? $payload['data']['status'] ?? null;

        if ($token) {
            $payment = Payement::where('paydunya_token', $token)->first();

            if ($payment) {
                if ($status === 'completed' || $status === 'success') {
                    $payment->update(['statut' => StatutPayementEnum::ACCEPTE]);
                    event(new PaiementAccepte($payment));
                } elseif ($status === 'failed' || $status === 'cancelled') {
                    $payment->update(['statut' => StatutPayementEnum::REFUSE]);
                    event(new PaiementRefuse($payment));
                }

                return response()->json(['message' => 'Paiement traité avec succès.'], Response::HTTP_OK);
            }
        }

        return response()->json(['message' => 'Jeton de paiement non trouvé.'], Response::HTTP_NOT_FOUND);
    }
}
