<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscriptionRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * Pour une inscription, nous permettons généralement à tout le monde d'y accéder.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Défini sur 'true' pour permettre l'inscription
    }

    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pseudo' => ['required', 'string', 'max:30', 'min:3', 'regex:/^[a-zA-Z0-9\s]+$/', 'unique:users'], // min:3 pour un pseudo plus flexible
//            'adresse' => ['required', 'string', 'max:255', 'min:5', 'regex:/^[a-zA-Z0-9\s]+$/'], // min:5 pour une adresse plus réaliste
//            'structure' => ['required', 'string', 'max:100', 'min:3', 'regex:/^[a-zA-Z0-9\s]+$/'], // min:3 pour structure
//            'sigle' => ['required', 'string', 'max:15', 'min:2', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:100', 'min:7', 'unique:users', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
//            'nom' => ['required', 'string', 'max:30', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
//            'prenom' => ['required', 'string', 'max:50', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'telephone1' => ['required', 'string', 'regex:/^(75|77|78|71|76|70)\d{7}$/', 'unique:users'], // Ajout 'unique:users'
//            'telephone2' => ['nullable', 'string', 'regex:/^(75|77|78|71|76|70)\d{7}$/', 'unique:users'], // Rendu nullable
//            'telephone_fixe' => ['nullable', 'string', 'regex:/^(33|30)\d{7}$/', 'unique:users'], // Rendu nullable
            'password' => [
                'required',
                'string',
                'min:7',
                'confirmed', // Ajouté pour la confirmation du mot de passe
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{7,}$/'
            ]
        ];
    }

    /**
     * Personnalise les messages d'erreur de validation.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'pseudo.required' => 'Le pseudo est obligatoire.',
            'pseudo.string' => 'Le pseudo doit être une chaîne de caractères.',
            'pseudo.max' => 'Le pseudo ne doit pas dépasser :max caractères.',
            'pseudo.min' => 'Le pseudo doit contenir au moins :min caractères.',
            'pseudo.regex' => 'Le pseudo ne doit contenir que des lettres, chiffres et espaces.',
            'pseudo.unique' => 'Ce pseudo est déjà utilisé.',

//            'adresse.required' => 'L\'adresse est obligatoire.',
//            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
//            'adresse.max' => 'L\'adresse ne doit pas dépasser :max caractères.',
//            'adresse.min' => 'L\'adresse doit contenir au moins :min caractères.',
//            'adresse.regex' => 'L\'adresse ne doit contenir que des lettres, chiffres et espaces.',
//
//            'structure.required' => 'La structure est obligatoire.',
//            'structure.string' => 'La structure doit être une chaîne de caractères.',
//            'structure.max' => 'La structure ne doit pas dépasser :max caractères.',
//            'structure.min' => 'La structure doit contenir au moins :min caractères.',
//            'structure.regex' => 'La structure ne doit contenir que des lettres, chiffres et espaces.',
//
//            'sigle.required' => 'Le sigle est obligatoire.',
//            'sigle.string' => 'Le sigle doit être une chaîne de caractères.',
//            'sigle.max' => 'Le sigle ne doit pas dépasser :max caractères.',
//            'sigle.min' => 'Le sigle doit contenir au moins :min caractères.',
//            'sigle.regex' => 'Le sigle ne doit contenir que des lettres, chiffres et espaces.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.string' => 'L\'adresse e-mail doit être une chaîne de caractères.',
            'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            'email.max' => 'L\'adresse e-mail ne doit pas dépasser :max caractères.',
            'email.min' => 'L\'adresse e-mail doit contenir au moins :min caractères.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'email.regex' => 'Le format de l\'adresse e-mail est invalide.',

//            'nom.required' => 'Le nom est obligatoire.',
//            'nom.string' => 'Le nom doit être une chaîne de caractères.',
//            'nom.max' => 'Le nom ne doit pas dépasser :max caractères.',
//            'nom.min' => 'Le nom doit contenir au moins :min caractères.',
//            'nom.regex' => 'Le nom ne doit contenir que des lettres et des espaces.',
//
//            'prenom.required' => 'Le prénom est obligatoire.',
//            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
//            'prenom.max' => 'Le prénom ne doit pas dépasser :max caractères.',
//            'prenom.min' => 'Le prénom doit contenir au moins :min caractères.',
//            'prenom.regex' => 'Le prénom ne doit contenir que des lettres et des espaces.',

            'telephone1.required' => 'Le numéro de téléphone principal est obligatoire.',
            'telephone1.string' => 'Le numéro de téléphone principal doit être une chaîne de caractères.',
            'telephone1.regex' => 'Le format du numéro de téléphone principal est invalide.',
            'telephone1.unique' => 'Ce numéro de téléphone est déjà utilisé.',

//            'telephone2.string' => 'Le numéro de téléphone secondaire doit être une chaîne de caractères.',
//            'telephone2.regex' => 'Le format du numéro de téléphone secondaire est invalide.',
//            'telephone2.unique' => 'Ce numéro de téléphone secondaire est déjà utilisé.',
//
//            'telephone_fixe.string' => 'Le numéro de téléphone fixe doit être une chaîne de caractères.',
//            'telephone_fixe.regex' => 'Le format du numéro de téléphone fixe est invalide.',
//            'telephone_fixe.unique' => 'Ce numéro de téléphone fixe est déjà utilisé.',

            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.'
        ];
    }
}
