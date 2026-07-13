<?php

declare(strict_types=1);

namespace App\Services\Paiements\PayDunya\Data;

/**
 * Résultat de la création d'une facture PayDunya (checkout invoice).
 */
final readonly class InvoiceResult
{
    /**
     * @param  array<string, mixed>  $raw  Réponse brute de l'API (pour journalisation/debug).
     */
    public function __construct(
        public bool $success,
        public ?string $token = null,
        public ?string $checkoutUrl = null,
        public ?string $responseCode = null,
        public ?string $message = null,
        public array $raw = [],
    ) {}

    public static function succes(string $token, string $checkoutUrl, array $raw = []): self
    {
        return new self(
            success: true,
            token: $token,
            checkoutUrl: $checkoutUrl,
            responseCode: '00',
            message: null,
            raw: $raw,
        );
    }

    public static function echec(string $message, ?string $responseCode = null, array $raw = []): self
    {
        return new self(
            success: false,
            token: null,
            checkoutUrl: null,
            responseCode: $responseCode,
            message: $message,
            raw: $raw,
        );
    }
}
