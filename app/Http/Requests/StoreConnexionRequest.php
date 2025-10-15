<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConnexionRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     * Pour une connexion standard, tout le monde est "autorisé" à tenter de se connecter.
     */
    public function authorize(): bool
    {
        return true; // Tout le monde peut tenter de se connecter.
    }

    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'pseudo' => ['required', 'string', 'min:3', 'max:255'],
            'password' => ['required', 'string', 'min:7'], // Une longueur minimale est recommandée
        ];
    }

    /**
     * Personnalise les messages d'erreur de validation (facultatif).
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'pseudo.required' => 'Le pseudo est obligatoire.',
            'pseudo.string' => 'Le pseudo doit être une chaîne de caractères.',
            'pseudo.min' => 'Le pseudo doit contenir au moins :min caractères.',
            'pseudo.max' => 'Le pseudo ne peut pas dépasser :max caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
        ];
    }
}
