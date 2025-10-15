<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscriptionRequest;
use App\Models\Souscription_structure;
use App\Models\User;
use App\Models\User_profile;
use App\Models\User_role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class InscriptionController extends Controller
{
    /**
     * Affiche la page d'inscription.
     *
     * @return \Illuminate\View\View
     */
    public function showInscription():View
    {
        return view('inscription'); // Crée la vue inscription.blade.php
    }

    /**
     * Traite l'inscription de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inscription(StoreInscriptionRequest $request)
    {
        // Validation des données reçues
//        $validator = Validator::make($request->all(), [
//            'telephone1' => ['required', 'string', 'max:255', 'unique:users,telephone1'],
//            'pseudo' => ['required', 'string', 'max:255', 'unique:users,pseudo'],
//            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
//            'password' => ['required', 'string', 'min:8', 'confirmed'], // "confirmed" pour vérifier password_confirmation
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json([
//                'success' => false,
//                'errors' => $validator->errors(),
//            ], 422);
//        }
        $user_matricule = 'user-'.$request->input('pseudo').'-'.now()->format('YmdHis');
        $profile_matricule = 'profil-'.$request->input('pseudo').'-'.now()->format('YmdHis');
//        $role_matricule = 'role-proprietaire-'.now()->format('YmdHis');

        // Création de l'utilisateur en base
        User::create([
            'user_matricule' => $user_matricule,
            'telephone1' => $request->input('telephone1'),
            'pseudo' => $request->input('pseudo'),
            'email' => $request->input('email'),
            'fonction' => 'simple-user',
            'password' => Hash::make($request->input('password')),
        ]);

        User_profile::create([
            'profile_matricule' => $profile_matricule,
            'user_matricule' => $user_matricule,
            'telephone1' => $request->input('telephone1'),
            'email' => $request->input('email'),
            'nom' => '',
            'prenom' => '',
            'adresse' => '',
            'profil' => 'simple-user',
            'photo_profil' => '',
        ]);

        // Connecter directement l'utilisateur après inscription
//        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie.',
            'redirect' => route('login'),
        ]);
    }
}
