<?php

namespace App\Http\Controllers\Api\V1\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Contrôleur pour gérer les notifications in-app des utilisateurs.
 */
class NotificationController extends Controller
{
    /**
     * Liste des notifications de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        return response()->json(Notification::where('user_id', $request->user()->id)->paginate());
    }

    /**
     * Afficher les détails d'une notification spécifique.
     */
    public function show($id)
    {
        return response()->json(Notification::findOrFail($id));
    }

    /**
     * Marquer une notification comme lue.
     */
    public function update(Request $request, $id)
    {
        $notif = Notification::findOrFail($id);
        $notif->update(['lu_a' => now()]);

        return response()->json($notif);
    }

    /**
     * Supprimer une notification.
     */
    public function destroy($id)
    {
        $notif = Notification::findOrFail($id);
        $notif->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
