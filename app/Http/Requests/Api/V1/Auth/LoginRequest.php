<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour l'authentification de l'utilisateur (Connexion).
 */
class LoginRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation appliquées à la requête.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'mot_de_passe' => ['required', 'string'],
        ];
    }
}
