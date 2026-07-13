<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Enums\StatutPayementEnum;
use App\Events\PaiementAccepte;
use App\Events\PaiementRefuse;
use App\Events\PaiementWebhookRecu;
use App\Http\Controllers\Controller;
use App\Models\Payement;
use App\Services\Paiements\PayDunya\Enums\PayDunyaPaymentStatus;
use App\Services\Paiements\PayDunya\Exceptions\PayDunyaException;
use App\Services\Paiements\PayDunya\PayDunyaWebhookVerifier;
use App\Services\Paiements\PayDunyaPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Webhook (IPN) PayDunya.
 *
 * Sécurité :
 *   1. Vérification de la signature SHA-512 (hash_equals) — rejet si invalide.
 *   2. Confirmation serveur-à-serveur du statut réel via l'API PayDunya : on ne
 *      fait JAMAIS confiance au statut posté dans le corps de la requête.
 *   3. Mise à jour idempotente sous verrou : un même paiement n'est crédité /
 *      confirmé qu'une seule fois, même en cas de rejeu du webhook.
 */
class PayDunyaWebhookController extends Controller
{
    public function __construct(
        protected PayDunyaWebhookVerifier $verifier,
        protected PayDunyaPaymentService $payDunya,
    ) {}

    public function handle(Request $request)
    {
        $payload = $request->all();

        // 1) Vérification de la signature — on ne journalise pas le payload complet
        // tant qu'il n'est pas authentifié (anti-pollution / anti-injection de logs).
        if (! $this->verifier->estValide($payload)) {
            Log::warning('PayDunyaWebhook : signature invalide, requête rejetée.', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Signature invalide.'], Response::HTTP_UNAUTHORIZED);
        }

        // À partir d'ici la notification est authentifiée : on peut l'auditer.
        event(new PaiementWebhookRecu($payload));

        $token = $this->verifier->extraireToken($payload);

        if (! $token) {
            Log::warning('PayDunyaWebhook : jeton absent du payload.');

            return response()->json(['message' => 'Jeton de paiement manquant.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 2) Statut réel confirmé par PayDunya (source de vérité).
        try {
            $statut = $this->payDunya->confirmer($token);
        } catch (PayDunyaException $e) {
            Log::error('PayDunyaWebhook : échec de confirmation.', [
                'token' => $token,
                'erreur' => $e->getMessage(),
            ]);

            // 5xx => PayDunya réessaiera la notification.
            return response()->json(['message' => 'Confirmation impossible.'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        // 3) Application idempotente du résultat, sous verrou de ligne.
        $traite = DB::transaction(function () use ($token, $statut) {
            $payement = Payement::where('paydunya_token', $token)->lockForUpdate()->first();

            if (! $payement) {
                return 'introuvable';
            }

            // Déjà finalisé : rejeu ignoré (idempotence).
            if ($payement->statut !== StatutPayementEnum::EN_COURS) {
                return 'deja_traite';
            }

            if ($statut === PayDunyaPaymentStatus::COMPLETED) {
                $payement->update(['statut' => StatutPayementEnum::ACCEPTE]);
                event(new PaiementAccepte($payement));

                return 'accepte';
            }

            if ($statut === PayDunyaPaymentStatus::CANCELLED) {
                $payement->update(['statut' => StatutPayementEnum::REFUSE]);
                event(new PaiementRefuse($payement));

                return 'refuse';
            }

            // PENDING / UNKNOWN : on ne change rien, PayDunya renotifiera.
            return 'en_attente';
        });

        if ($traite === 'introuvable') {
            return response()->json(['message' => 'Jeton de paiement non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        Log::info('PayDunyaWebhook : traitement terminé.', [
            'token' => $token,
            'statut_confirme' => $statut->value,
            'resultat' => $traite,
        ]);

        return response()->json(['message' => 'Notification traitée.', 'resultat' => $traite], Response::HTTP_OK);
    }
}
