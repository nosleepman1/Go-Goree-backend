<?php

namespace App\Http\Controllers\Api\V1\Voyages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Voyages\StoreTrajetRequest;
use App\Http\Requests\Api\V1\Voyages\UpdateTrajetRequest;
use App\Models\Trajet;
use Illuminate\Http\Response;

/**
 * Contrôleur pour gérer les trajets et horaires associés (CRUD administratif).
 */
class TrajetController extends Controller
{
    /**
     * Liste des trajets.
     */
    public function index()
    {
        return response()->json(Trajet::paginate());
    }

    /**
     * Enregistrer un nouveau trajet (avec heure de départ et durée).
     */
    public function store(StoreTrajetRequest $request)
    {
        $record = Trajet::create($request->validated());

        return response()->json($record, Response::HTTP_CREATED);
    }

    /**
     * Afficher les détails d'un trajet spécifique.
     */
    public function show($id)
    {
        return response()->json(Trajet::findOrFail($id));
    }

    /**
     * Mettre à jour un trajet.
     */
    public function update(UpdateTrajetRequest $request, $id)
    {
        $record = Trajet::findOrFail($id);
        $record->update($request->validated());

        return response()->json($record);
    }

    /**
     * Supprimer un trajet.
     */
    public function destroy($id)
    {
        $record = Trajet::findOrFail($id);
        $record->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
