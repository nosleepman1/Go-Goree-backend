<?php

namespace App\Http\Controllers\Api\V1\Residents;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Contrôleur pour gérer les résidents (CRUD administratif).
 */
class ResidentController extends Controller
{
    /**
     * Liste des résidents.
     */
    public function index()
    {
        return response()->json(Resident::paginate());
    }

    /**
     * Enregistrer un nouveau résident.
     */
    public function store(Request $request)
    {
        $record = Resident::create($request->all());

        return response()->json($record, Response::HTTP_CREATED);
    }

    /**
     * Afficher les détails d'un résident spécifique.
     */
    public function show($id)
    {
        return response()->json(Resident::findOrFail($id));
    }

    /**
     * Mettre à jour les informations d'un résident.
     */
    public function update(Request $request, $id)
    {
        $record = Resident::findOrFail($id);
        $record->update($request->all());

        return response()->json($record);
    }

    /**
     * Supprimer un résident.
     */
    public function destroy($id)
    {
        $record = Resident::findOrFail($id);
        $record->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
