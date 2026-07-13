<?php

declare(strict_types=1);

namespace App\Services\Paiements\PayDunya;

use App\Services\Paiements\PayDunya\Data\InvoiceResult;
use App\Services\Paiements\PayDunya\Data\PaymentIntent;
use App\Services\Paiements\PayDunya\Enums\PayDunyaPaymentStatus;
use App\Services\Paiements\PayDunya\Exceptions\PayDunyaException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implémentation réelle du client PayDunya (appels HTTP vers l'API v1).
 *
 * Endpoints utilisés :
 *   - POST {base}/checkout-invoice/create
 *   - GET  {base}/checkout-invoice/confirm/{token}
 *
 * L'URL de base dépend de l'environnement (sandbox vs production) et les
 * requêtes sont authentifiées par les en-têtes PAYDUNYA-*.
 */
class PayDunyaHttpClient implements PayDunyaClientInterface
{
    /**
     * @param  array<string, mixed>  $config  Sous-arbre config('paydunya').
     */
    public function __construct(protected array $config) {}

    public function createInvoice(PaymentIntent $intent): InvoiceResult
    {
        $body = [
            'invoice' => [
                'total_amount' => $intent->montant,
                'description' => $intent->description,
                'items' => [
                    'item_0' => [
                        'name' => $intent->description,
                        'quantity' => 1,
                        'unit_price' => (string) $intent->montant,
                        'total_price' => (string) $intent->montant,
                    ],
                ],
            ],
            'store' => array_filter([
                'name' => $this->config['store']['name'] ?? null,
                'tagline' => $this->config['store']['tagline'] ?? null,
                'phone' => $this->config['store']['phone'] ?? null,
                'postal_address' => $this->config['store']['postal_address'] ?? null,
                'website_url' => $this->config['store']['website_url'] ?? null,
                'logo_url' => $this->config['store']['logo_url'] ?? null,
            ], static fn ($v) => $v !== null && $v !== ''),
            'actions' => [
                'cancel_url' => $this->config['actions']['cancel_url'] ?? null,
                'return_url' => $this->config['actions']['return_url'] ?? null,
                'callback_url' => $this->config['actions']['callback_url'] ?? null,
            ],
            'custom_data' => array_merge($intent->customData, [
                'reference' => $intent->reference,
            ]),
        ];

        $response = $this->request('post', 'checkout-invoice/create', $body);

        $code = (string) ($response['response_code'] ?? '');
        $token = $response['token'] ?? null;
        $checkoutUrl = $response['checkout_url'] ?? ($response['response_text'] ?? null);

        if ($code === '00' && is_string($token) && is_string($checkoutUrl)) {
            return InvoiceResult::succes($token, $checkoutUrl, $response);
        }

        Log::warning('PayDunyaHttpClient : échec createInvoice', [
            'reference' => $intent->reference,
            'response_code' => $code,
            'response' => $response,
        ]);

        return InvoiceResult::echec(
            (string) ($response['response_text'] ?? 'Échec de création de la facture PayDunya.'),
            $code !== '' ? $code : null,
            $response,
        );
    }

    public function confirmInvoice(string $token): PayDunyaPaymentStatus
    {
        $response = $this->request('get', 'checkout-invoice/confirm/'.rawurlencode($token));

        $statut = $response['status'] ?? ($response['invoice']['status'] ?? null);

        return PayDunyaPaymentStatus::depuisApi(is_string($statut) ? $statut : null);
    }

    /**
     * Effectue une requête HTTP authentifiée vers l'API PayDunya.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    protected function request(string $method, string $path, array $body = []): array
    {
        $this->assertConfigured();

        $url = rtrim($this->baseUrl(), '/').'/'.ltrim($path, '/');

        try {
            $request = Http::withHeaders($this->headers())
                ->timeout((int) ($this->config['http']['timeout'] ?? 30))
                ->retry(
                    (int) ($this->config['http']['retries'] ?? 2),
                    (int) ($this->config['http']['retry_delay_ms'] ?? 300),
                    throw: false,
                )
                ->acceptJson()
                ->asJson();

            $response = $method === 'get'
                ? $request->get($url)
                : $request->post($url, $body);
        } catch (ConnectionException $e) {
            throw new PayDunyaException('PayDunya injoignable : '.$e->getMessage(), previous: $e);
        }

        if ($response->serverError()) {
            throw new PayDunyaException('PayDunya a renvoyé une erreur serveur ('.$response->status().').');
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new PayDunyaException('Réponse PayDunya illisible (non JSON).');
        }

        return $data;
    }

    protected function baseUrl(): string
    {
        return ($this->config['environment'] ?? 'test') === 'live'
            ? ($this->config['urls']['live'] ?? 'https://app.paydunya.com/api/v1')
            : ($this->config['urls']['sandbox'] ?? 'https://app.paydunya.com/sandbox-api/v1');
    }

    /**
     * @return array<string, string>
     */
    protected function headers(): array
    {
        return [
            'PAYDUNYA-MASTER-KEY' => (string) ($this->config['keys']['master'] ?? ''),
            'PAYDUNYA-PRIVATE-KEY' => (string) ($this->config['keys']['private'] ?? ''),
            'PAYDUNYA-TOKEN' => (string) ($this->config['keys']['token'] ?? ''),
        ];
    }

    protected function assertConfigured(): void
    {
        foreach (['master', 'private', 'token'] as $cle) {
            if (empty($this->config['keys'][$cle])) {
                throw new PayDunyaException(
                    "Configuration PayDunya incomplète : la clé « {$cle} » est manquante. ".
                    'Renseignez PAYDUNYA_MASTER_KEY, PAYDUNYA_PRIVATE_KEY et PAYDUNYA_TOKEN dans .env.'
                );
            }
        }
    }
}
