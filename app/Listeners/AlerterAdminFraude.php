<?php

namespace App\Listeners;

use App\Events\FraudeDetectee;
use App\Mail\AlerteFraudeMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Écouteur pour alerter les administrateurs par e-mail lorsqu'une fraude est détectée.
 */
class AlerterAdminFraude
{
    /**
     * Traiter l'événement.
     */
    public function handle(FraudeDetectee $event): void
    {
        $alerte = $event->alerte;

        if (! $alerte) {
            return;
        }

        // Trouver tous les administrateurs
        $admins = User::whereHas('role', function ($query) {
            $query->where('nom', 'Admin');
        })->get();

        if ($admins->isNotEmpty()) {
            foreach ($admins as $admin) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new AlerteFraudeMail($alerte));
                }
            }

            Log::info("AlerterAdminFraude : E-mail d'alerte de fraude envoyé à ".$admins->count().' administrateurs.');
        }
    }
}
