<?php

namespace App\Http\Controllers\Api\V1\Billetterie;

use App\Enums\ResultatScanEnum;
use App\Enums\StatutBilletEnum;
use App\Events\BilletScanne;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BilletResource;
use App\Models\Billet;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour gérer le scan et la validation d'un billet lors de l'embarquement.
 */
class ScanController extends Controller
{
    /**
     * Liste des scans enregistrés.
     */
    public function index()
    {
        return response()->json(Scan::with('billet')->paginate());
    }

    /**
     * Scanner un billet via son QR code unique.
     */
    public function store(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($request) {
            $billet = Billet::where('qr_token', $request->qr_token)->first();

            // Si le billet n'existe pas
            if (! $billet) {
                return response()->json([
                    'message' => 'Billet non trouvé.',
                    'resultat' => ResultatScanEnum::NON_EMBARQUE->value,
                ], Response::HTTP_NOT_FOUND);
            }

            $resultat = ResultatScanEnum::VALIDE;

            // Déterminer le résultat du scan selon le statut du billet
            if ($billet->statut === StatutBilletEnum::UTILISE) {
                $resultat = ResultatScanEnum::DEJA_SCANNE;
            } elseif ($billet->statut === StatutBilletEnum::EXPIRE) {
                $resultat = ResultatScanEnum::EXPIRE;
            } elseif ($billet->statut !== StatutBilletEnum::PAYE) {
                $resultat = ResultatScanEnum::NON_EMBARQUE;
            }

            // Enregistrer la tentative de scan
            $scan = Scan::create([
                'billet_id' => $billet->id,
                'resultat' => $resultat,
            ]);

            // Mettre à jour le billet s'il est validé
            if ($resultat === ResultatScanEnum::VALIDE) {
                $billet->update([
                    'statut' => StatutBilletEnum::UTILISE,
                ]);
            }

            // Déclencher l'événement du scan de billet
            event(new BilletScanne($scan));

            return response()->json([
                'message' => $resultat === ResultatScanEnum::VALIDE ? 'Scan validé avec succès.' : 'Scan invalide.',
                'resultat' => $resultat->value,
                'scan' => $scan,
                'billet' => new BilletResource($billet->load(['voyage', 'tarif'])),
            ], $resultat === ResultatScanEnum::VALIDE ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
        });
    }

    /**
     * Afficher les détails d'un scan spécifique.
     */
    public function show($id)
    {
        return response()->json(Scan::with('billet')->findOrFail($id));
    }
}
