<?php

namespace App\Http\Requests;

use App\Models\User_profile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProfilRequest extends FormRequest
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
        $user_matricule = Auth::user()->user_matricule;
        $user_profile = User_profile::where('user_matricule', $user_matricule)->first();
        return [
            'nom'           => 'required|string|max:255',
            'prenom'        => 'required|string|max:255',
            'email'         => 'required|email|unique:user_profiles,email,' . $user_profile->user_matricule . ',user_matricule',
            'telephone1'    => 'required|string|max:15|unique:user_profiles,telephone1,' . $user_profile->user_matricule . ',user_matricule',
            'telephone2'    => 'nullable|string|max:15|unique:user_profiles,telephone2,' . $user_profile->user_matricule . ',user_matricule',
            'adresse'       => 'nullable|string|max:255',
            'profil'        => 'required|in:vendeur,livreur,mixte,simple_user',
            'photo_profil' => 'required|file|mimes:jpeg,png,jpg,webp|max:2048',
            'date_naissance'=> 'nullable|date',
            'sexe'          => 'nullable|in:H,F',
            'cni'           => 'nullable|string|max:20|unique:user_profiles,cni,' . $user_profile->user_matricule . ',user_matricule',
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
            // Messages d'erreur personnalisés
            'nom.required'            => 'Le nom est obligatoire.',
            'nom.string'              => 'Le nom doit être une chaîne de caractères.',
            'nom.max'                 => 'Le nom ne doit pas dépasser 255 caractères.',

            'prenom.required'         => 'Le prénom est obligatoire.',
            'prenom.string'           => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max'              => 'Le prénom ne doit pas dépasser 255 caractères.',

            'email.required'          => 'L\'email est obligatoire.',
            'email.email'             => 'Le format de l\'email est invalide.',
            'email.unique'            => 'Cet email est déjà utilisé.',

            'telephone1.required'     => 'Le numéro principal est obligatoire.',
            'telephone1.string'       => 'Le numéro principal doit être une chaîne de caractères.',
            'telephone1.max'          => 'Le numéro principal ne doit pas dépasser 15 caractères.',
            'telephone1.unique'       => 'Ce numéro principal est déjà utilisé.',

            'telephone2.string'       => 'Le numéro secondaire doit être une chaîne de caractères.',
            'telephone2.max'          => 'Le numéro secondaire ne doit pas dépasser 15 caractères.',
            'telephone2.unique'       => 'Ce numéro secondaire est déjà utilisé.',

            'adresse.string'          => 'L\'adresse doit être une chaîne de caractères.',
            'adresse.max'             => 'L\'adresse ne doit pas dépasser 255 caractères.',

            'profil.required'         => 'Le profil est obligatoire.',
            'profil.in'               => 'Le profil doit être vendeur, livreur, mixte ou simple_user.',

            'photo_profil.image'      => 'Le fichier doit être une image.',
            'photo_profil.mimes'      => 'L\'image doit être au format jpeg, png, jpg.',
            'photo_profil.max'        => 'L\'image ne doit pas dépasser 2 Mo.',
            'photo_profil.required'        => "La photo de profil est obligatoire",

            'date_naissance.date'     => 'La date de naissance doit être une date valide.',

            'sexe.in'                 => 'Le sexe doit être H (Homme) ou F (Femme).',

            'cni.string'              => 'Le numéro de CNI doit être une chaîne de caractères.',
            'cni.max'                 => 'Le numéro de CNI ne doit pas dépasser 20 caractères.',
            'cni.unique'              => 'Ce numéro de CNI est déjà utilisé.',
        ];
    }
}
