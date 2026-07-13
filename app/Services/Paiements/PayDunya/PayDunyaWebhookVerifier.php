<?php

declare(strict_types=1);

namespace App\Services\Paiements\PayDunya;

use Illuminate\Support\Arr;

/**
 * Vérifie l'authenticité d'une notification instantanée (IPN) PayDunya.
 *
 * PayDunya signe chaque IPN avec le SHA-512 de la MASTER KEY, transmis dans le
 * champ "hash" du payload. On recalcule ce hash côté serveur et on le compare
 * en temps constant (hash_equals) : toute notification non signée ou dont la
 * signature ne correspond pas est rejetée.
 *
 * En mode "fake" (pas de master key), on utilise le secret local dédié
 * (paydunya.fake.secret) pour que le webhook reste authentifié dans les tests.
 */
class PayDunyaWebhookVerifier
{
    /**
     * @param  array<string, mixed>  $config  Sous-arbre config('paydunya').
     */
    public function __construct(protected array $config) {}

    /**
     * Le secret courant selon le pilote (master key en réel, secret fake sinon).
     */
    public function secret(): string
    {
        if (($this->config['driver'] ?? 'fake') === 'http') {
            return (string) ($this->config['keys']['master'] ?? '');
        }

        return (string) ($this->config['fake']['secret'] ?? '');
    }

    /**
     * Signature attendue = SHA-512 du secret.
     */
    public function signatureAttendue(): string
    {
        return hash('sha512', $this->secret());
    }

    /**
     * Vérifie la signature d'un payload IPN.
     *
     * @param  array<string, mixed>  $payload
     */
    public function estValide(array $payload): bool
    {
        // Vérification désactivable explicitement (déconseillé hors debug local).
        if (! ($this->config['webhook']['require_signature'] ?? true)) {
            return true;
        }

        // Sans secret configuré, on refuse par sécurité (fail-closed).
        if ($this->secret() === '') {
            return false;
        }

        $fournie = $this->extraireSignature($payload);

        if (! is_string($fournie) || $fournie === '') {
            return false;
        }

        return hash_equals($this->signatureAttendue(), $fournie);
    }

    /**
     * Extrait la signature "hash" du payload, quelle que soit sa forme
     * (racine, sous-clé "data").
     *
     * @param  array<string, mixed>  $payload
     */
    public function extraireSignature(array $payload): ?string
    {
        $hash = $payload['hash'] ?? Arr::get($payload, 'data.hash');

        return is_string($hash) ? $hash : null;
    }

    /**
     * Extrait le jeton de facture du payload IPN.
     *
     * @param  array<string, mixed>  $payload
     */
    public function extraireToken(array $payload): ?string
    {
        $token = $payload['token']
            ?? Arr::get($payload, 'data.token')
            ?? Arr::get($payload, 'invoice.token')
            ?? Arr::get($payload, 'data.invoice.token');

        return is_string($token) ? $token : null;
    }
}
