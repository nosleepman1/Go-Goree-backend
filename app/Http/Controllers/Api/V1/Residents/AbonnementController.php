<?php

namespace App\Http\Controllers\Api\V1\Residents;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Contrôleur pour gérer les abonnements des résidents (CRUD administratif).
 */
class AbonnementController extends Controller
{
    /**
     * Liste des abonnements.
     */
    public function index()
    {
        return response()->json(Abonnement::paginate());
    }

    /**
     * Créer un nouvel abonnement.
     */
    public function store(Request $request)
    {
        $record = Abonnement::create($request->all());

        return response()->json($record, Response::HTTP_CREATED);
    }

    /**
     * Afficher les détails d'un abonnement spécifique.
     */
    public function show($id)
    {
        return response()->json(Abonnement::findOrFail($id));
    }

    /**
     * Mettre à jour un abonnement.
     */
    public function update(Request $request, $id)
    {
        $record = Abonnement::findOrFail($id);
        $record->update($request->all());

        return response()->json($record);
    }

    /**
     * Supprimer un abonnement.
     */
    public function destroy($id)
    {
        $record = Abonnement::findOrFail($id);
        $record->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
