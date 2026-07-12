<?php

namespace App\Listeners;

use App\Events\RapportJournalierGenere;
use App\Mail\RapportJournalierMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Écouteur pour envoyer le rapport d'activité journalier à tous les administrateurs.
 */
class EnvoyerRapportJournalierAuxAdmins
{
    /**
     * Traiter l'événement.
     */
    public function handle(RapportJournalierGenere $event): void
    {
        // Récupérer tous les administrateurs
        $admins = User::whereHas('role', function ($query) {
            $query->where('nom', 'Admin');
        })->get();

        if ($admins->isNotEmpty()) {
            foreach ($admins as $admin) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new RapportJournalierMail($event->donnees));
                }
            }

            $date = $event->donnees['date'] ?? now()->toDateString();
            Log::info("EnvoyerRapportJournalierAuxAdmins : Rapport journalier du {$date} envoyé à ".$admins->count().' administrateurs.');
        }
    }
}
