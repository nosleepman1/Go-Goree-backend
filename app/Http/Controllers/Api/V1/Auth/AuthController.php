<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\LoginResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

/**
 * Contrôleur pour l'authentification des utilisateurs via Sanctum (Login, Logout, Profil).
 */
class AuthController extends Controller
{
    /**
     * Authentifier un utilisateur et générer un jeton d'accès API.
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
            return response()->json([
                'message' => 'Identifiants incorrects.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->active) {
            return response()->json([
                'message' => 'Votre compte est désactivé.',
            ], Response::HTTP_FORBIDDEN);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return new LoginResource([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role'),
        ]);
    }

    /**
     * Déconnecter l'utilisateur (révoquer son jeton d'accès actuel).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnecté avec succès.',
        ]);
    }

    /**
     * Obtenir les informations de profil de l'utilisateur connecté.
     */
    public function me(Request $request)
    {
        return new UserResource($request->user()->load('role'));
    }
}
