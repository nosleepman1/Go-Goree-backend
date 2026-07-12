<?php

namespace App\Services\Residents;

use App\Enums\DemandeResidenceEnum;
use App\Events\DemandeResidenceAcceptee;
use App\Events\DemandeResidenceRefusee;
use App\Models\DemandeResidence;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Service pour la validation administrative des demandes de résidence.
 * Gère l'acceptation ou le refus des demandes.
 */
class DemandeResidenceValidationService
{
    /**
     * Valider une demande de résidence.
     */
    public function valider(DemandeResidence $demande, User $admin): void
    {
        DB::transaction(function () use ($demande, $admin) {
            $demande->update([
                'statut' => DemandeResidenceEnum::ACCEPTEE,
                'valide_par' => $admin->id,
                'date_validation' => now(),
            ]);

            event(new DemandeResidenceAcceptee($demande));
        });
    }

    /**
     * Refuser une demande de résidence.
     */
    public function refuser(DemandeResidence $demande, string $motif, User $admin): void
    {
        $demande->update([
            'statut' => DemandeResidenceEnum::REFUSEE,
            'motif_refus' => $motif,
            'valide_par' => $admin->id,
            'date_validation' => now(),
        ]);

        event(new DemandeResidenceRefusee($demande));
    }
}
