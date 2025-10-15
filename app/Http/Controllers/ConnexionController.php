<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\User_profile;
use App\Models\User_role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;

class ConnexionController extends Controller
{
    /**
     * Affiche la page de connexion.
     *
     * @return \Illuminate\View\View
     */
    public function showConnexion():View
    {
        return view('connexion'); // Vue connexion.blade.php
    }

    /**
     * Traite la tentative de connexion avec email et mot de passe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validation
        $credentials = $request->validate([
            'pseudo' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Authentification

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // ✅ Stocker des variables dans la session
            $user = Auth::user(); // Récupérer l'utilisateur connecté
            $user_matricule = Auth::user()->user_matricule;
            $user_profile = User_profile::where('user_matricule', $user_matricule)->first();

            session()->put('pseudo', $user->pseudo);
            session()->put('fonction', $user->fonction);
            session()->put('photo_profil', $user_profile->photo_profil);


            if ($user->fonction === 'caissier')
            {
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                    'redirect' => route('vente'),
                    'pseudo' => $user->pseudo, // Optionnel pour JS
                ]);
            }elseif($user->fonction === 'proprietaire' || $user->fonction === 'gerant'){
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                    'redirect' => route('dashboard'),
                    'pseudo' => $user->pseudo, // Optionnel pour JS
                ]);
            }elseif($user->fonction === 'simple-user')
            {
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                    'redirect' => route('accueil'),
                    'pseudo' => $user->pseudo, // Optionnel pour JS
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'pseudo' => ['Identifiants invalides.'],
            ],
        ], 422);
    }


    /**
     * Déconnecte l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/connexion'); // ou route('accueil')
    }
}
