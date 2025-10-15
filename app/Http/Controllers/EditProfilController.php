<?php
namespace App\Http\Controllers;

use App\Models\User_profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditProfilController extends Controller
{
    // Récupérer les infos du profil de l'utilisateur connecté
    public function show()
    {
        $user_matricule = Auth::user()->user_matricule;
        $user_profile = User_profile::firstOrFail($user_matricule);

        return response()->json($user_profile);
    }

    public function check_password(Request $request)
    {
        $request->validate(
            [
                'password' => 'required|string|min:7',
            ],
            [
                'password.required' => '⚠️ Le mot de passe est obligatoire.',
                'password.min'      => '⚠️ Le mot de passe doit contenir au moins 7 caractères.',
                'password.string'   => '⚠️ Le mot de passe doit être une chaîne de caractères.',
            ]
        );

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            // ✅ Mot de passe correct
            return response()->json(['success' => 'passwordCheckedRas', 'message' => 'Mot de passe vérifié.']);
        } else {
            // ❌ Mauvais mot de passe
            return response()->json(['success' => 'passwordWrongKo', 'message' => 'Mot de passe incorrect.']);
        }

    }

    // Mettre à jour le profil
    public function update(Request $request)
    {
        $user_matricule = Auth::user()->user_matricule;

        $user_profile = User_profile::firstOrFail($user_matricule);

        $validated = $request->validate([
            'nom'           => 'required|string|max:255',
            'prenom'        => 'required|string|max:255',
            'email'         => 'required|email|unique:user_profiles,email,' . $user_profile->user_matricule . ',user_matricule',
            'telephone1'    => 'required|string|max:15|unique:user_profiles,telephone1,' . $user_profile->user_matricule . ',user_matricule',
            'telephone2'    => 'nullable|string|max:15|unique:user_profiles,telephone2,' . $user_profile->user_matricule . ',user_matricule',
            'adresse'       => 'nullable|string|max:255',
            'profil'        => 'required|in:vendeur,livreur,mixte,simple_user',
            'photo_profil'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_naissance'=> 'nullable|date',
            'sexe'          => 'nullable|in:M,F',
            'cni'           => 'nullable|string|max:20|unique:user_profiles,cni,' . $user_profile->user_matricule . ',user_matricule',
            ], [
            // Messages d'erreur personnalisés
            'nom.required'          => 'Le nom est obligatoire.',
            'prenom.required'       => 'Le prénom est obligatoire.',
            'email.required'        => 'L\'email est obligatoire.',
            'email.email'           => 'Le format de l\'email est invalide.',
            'email.unique'          => 'Cet email est déjà utilisé.',
            'profil.required'       => 'Le profil est obligatoire.',
            'profil.in'             => 'Le profil doit être vendeur, livreur ou mixt.',
            'photo_profil.image'    => 'Le fichier doit être une image.',
            'photo_profil.mimes'    => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'photo_profil.max'      => 'L\'image ne doit pas dépasser 2 Mo.',
            'sexe.in'               => 'Le sexe doit être M ou F.',
        ]);

        $user_profile->update($validated);

        return response()->json([
            'success' => 'updateProfilRas',
            'message' => 'Profil mis à jour avec succès !',
            'data' => $user_profile
        ]);
    }
}
