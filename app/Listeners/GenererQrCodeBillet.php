<?php

namespace App\Listeners;

use App\Events\BilletAchete;
use App\Services\Billetterie\SubServices\BilletQrTokenGeneratorService;
use Illuminate\Support\Facades\Log;

/**
 * Écouteur pour s'assurer de la génération et de la validation du code QR associé au billet acheté.
 */
class GenererQrCodeBillet
{
    /**
     * Créer une nouvelle instance de l'écouteur.
     */
    public function __construct(protected BilletQrTokenGeneratorService $qrGenerator) {}

    /**
     * Traiter l'événement.
     */
    public function handle(BilletAchete $event): void
    {
        $billet = $event->billet;

        // Si le token QR n'a pas encore été généré
        if (! $billet->qr_token) {
            $billet->update([
                'qr_token' => $this->qrGenerator->generate(),
            ]);
        }

        Log::info("GenererQrCodeBillet : Code QR généré visuellement et associé pour le billet ID {$billet->id} (Token: {$billet->qr_token}).");
    }
}
