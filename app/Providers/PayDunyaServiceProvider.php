<?php

namespace App\Providers;

use App\Services\Paiements\PayDunya\FakePayDunyaClient;
use App\Services\Paiements\PayDunya\PayDunyaClientInterface;
use App\Services\Paiements\PayDunya\PayDunyaHttpClient;
use App\Services\Paiements\PayDunya\PayDunyaWebhookVerifier;
use Illuminate\Support\ServiceProvider;

/**
 * Enregistre l'intégration PayDunya et choisit l'implémentation du client
 * (réelle ou simulée) en fonction de la configuration (config/paydunya.php).
 */
class PayDunyaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Client PayDunya : http (réseau réel) ou fake (simulation), selon le driver.
        $this->app->singleton(PayDunyaClientInterface::class, function ($app) {
            $config = $app['config']->get('paydunya', []);

            return ($config['driver'] ?? 'fake') === 'http'
                ? new PayDunyaHttpClient($config)
                : new FakePayDunyaClient($config);
        });

        // Vérificateur de signature du webhook (partagé).
        $this->app->singleton(PayDunyaWebhookVerifier::class, function ($app) {
            return new PayDunyaWebhookVerifier($app['config']->get('paydunya', []));
        });
    }

    public function boot(): void
    {
        //
    }
}
