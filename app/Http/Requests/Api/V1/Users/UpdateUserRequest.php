<?php

namespace App\Http\Requests\Api\V1\Users;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la mise à jour d'un utilisateur.
 */
class UpdateUserRequest extends FormRequest
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
        $id = $this->route('user') ?? $this->route('id');

        return [
            'prenom' => ['sometimes', 'string', 'max:255'],
            'nom' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'mot_de_passe' => ['sometimes', 'string', 'min:8'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['sometimes', 'exists:roles,id'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
