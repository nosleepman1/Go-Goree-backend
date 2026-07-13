<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pilote (driver)
    |--------------------------------------------------------------------------
    |
    | Détermine QUELLE implémentation de PayDunyaClientInterface est injectée :
    |   - "http" : appels réseau réels vers l'API PayDunya (prod & sandbox réelle)
    |   - "fake" : client simulé, aucun réseau, tokens déterministes (tests / local)
    |
    | Par défaut on déduit le pilote de PAYDUNYA_MODE pour rester rétro-compatible
    | avec l'ancien scaffolding : mode "live" => http, tout le reste => fake.
    |
    */

    'driver' => env('PAYDUNYA_DRIVER', env('PAYDUNYA_MODE', 'fake') === 'live' ? 'http' : 'fake'),

    /*
    |--------------------------------------------------------------------------
    | Environnement PayDunya (endpoint du pilote http)
    |--------------------------------------------------------------------------
    |
    | "test" => API sandbox (compte fictif PayDunya)
    | "live" => API de production
    |
    */

    'environment' => env('PAYDUNYA_ENVIRONMENT', 'test'),

    'urls' => [
        'live' => 'https://app.paydunya.com/api/v1',
        'sandbox' => 'https://app.paydunya.com/sandbox-api/v1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Clés d'API PayDunya
    |--------------------------------------------------------------------------
    |
    | Obtenues depuis le tableau de bord PayDunya. NE JAMAIS committer de vraies
    | valeurs : elles vivent uniquement dans le fichier .env (non versionné).
    |
    */

    'keys' => [
        'master' => env('PAYDUNYA_MASTER_KEY'),
        'private' => env('PAYDUNYA_PRIVATE_KEY'),
        'public' => env('PAYDUNYA_PUBLIC_KEY'),
        'token' => env('PAYDUNYA_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Informations boutique (affichées sur la page de paiement PayDunya)
    |--------------------------------------------------------------------------
    */

    'store' => [
        'name' => env('PAYDUNYA_STORE_NAME', env('APP_NAME', 'Go Gorée')),
        'tagline' => env('PAYDUNYA_STORE_TAGLINE', 'Billetterie des chaloupes de Gorée'),
        'phone' => env('PAYDUNYA_STORE_PHONE'),
        'postal_address' => env('PAYDUNYA_STORE_ADDRESS'),
        'website_url' => env('PAYDUNYA_STORE_WEBSITE', env('APP_URL')),
        'logo_url' => env('PAYDUNYA_STORE_LOGO'),
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs de redirection / callback
    |--------------------------------------------------------------------------
    |
    | callback_url = URL du webhook IPN appelée par PayDunya (serveur -> serveur).
    | return_url / cancel_url = redirections navigateur après paiement.
    |
    */

    'actions' => [
        'callback_url' => env('PAYDUNYA_WEBHOOK_URL', rtrim((string) env('APP_URL'), '/').'/webhooks/paydunya'),
        'return_url' => env('PAYDUNYA_RETURN_URL', rtrim((string) env('APP_URL'), '/').'/paiement/retour'),
        'cancel_url' => env('PAYDUNYA_CANCEL_URL', rtrim((string) env('APP_URL'), '/').'/paiement/annule'),
    ],

    'currency' => env('PAYDUNYA_CURRENCY', 'XOF'),

    /*
    |--------------------------------------------------------------------------
    | Réglages HTTP (pilote http)
    |--------------------------------------------------------------------------
    */

    'http' => [
        'timeout' => (int) env('PAYDUNYA_HTTP_TIMEOUT', 30),
        'retries' => (int) env('PAYDUNYA_HTTP_RETRIES', 2),
        'retry_delay_ms' => (int) env('PAYDUNYA_HTTP_RETRY_DELAY_MS', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sécurité du webhook (IPN)
    |--------------------------------------------------------------------------
    |
    | PayDunya signe ses IPN avec le SHA-512 de votre MASTER KEY (champ "hash").
    | On rejette toute notification dont la signature ne correspond pas.
    |
    | En mode "fake", il n'y a pas de master key : on signe/vérifie alors avec
    | un secret local dédié (PAYDUNYA_FAKE_SECRET) pour que le webhook reste
    | authentifié de bout en bout, y compris dans les tests.
    |
    */

    'webhook' => [
        'require_signature' => (bool) env('PAYDUNYA_WEBHOOK_REQUIRE_SIGNATURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pilote "fake"
    |--------------------------------------------------------------------------
    */

    'fake' => [
        // La confirmation renvoie automatiquement "completed" (paiement réputé payé).
        'auto_complete' => (bool) env('PAYDUNYA_FAKE_AUTO_COMPLETE', true),
        // Secret servant à signer/vérifier les IPN simulés (compte fictif).
        'secret' => env('PAYDUNYA_FAKE_SECRET', 'paydunya-fake-secret-change-me'),
        // URL de « page de paiement » factice renvoyée par createInvoice.
        'checkout_base' => env('PAYDUNYA_FAKE_CHECKOUT_BASE', rtrim((string) env('APP_URL'), '/').'/paydunya/fake/checkout'),
    ],

];
