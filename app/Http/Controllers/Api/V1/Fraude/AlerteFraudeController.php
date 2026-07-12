<?php

namespace App\Http\Controllers\Api\V1\Fraude;

use App\Http\Controllers\Controller;
use App\Models\AlerteFraude;
use Illuminate\Http\Request;

/**
 * Contrôleur pour gérer les alertes de fraude (visualisation et traitement par les administrateurs).
 */
class AlerteFraudeController extends Controller
{
    /**
     * Liste des alertes de fraude.
     */
    public function index()
    {
        return response()->json(AlerteFraude::paginate());
    }

    /**
     * Afficher les détails d'une alerte spécifique.
     */
    public function show($id)
    {
        return response()->json(AlerteFraude::findOrFail($id));
    }

    /**
     * Mettre à jour le statut ou le traitement d'une alerte.
     */
    public function update(Request $request, $id)
    {
        $alert = AlerteFraude::findOrFail($id);
        $alert->update($request->only(['statut', 'traite_par']));

        return response()->json($alert);
    }
}
