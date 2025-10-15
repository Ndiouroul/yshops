<?php

namespace App\Http\Middleware;

use App\Models\User_profile;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckShoper
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('connexion');
        }

        $user_function = Auth::user()->fonction;

        if ($user_function === 'simple-user') {
            abort(403, 'Accès refusé.');
        }

        $user_matricule = Auth::user()->user_matricule;

        $profil = User_profile::where('user_matricule', $user_matricule)->first();
        if($profil->nom !== "" && $profil->nom !== null){
            if ($profil->prenom !== '' && $profil->prenom !== null){
                if($profil->sexe === "F" || $profil->sexe === "H"){
                    return $next($request);
                }else{
                    return redirect()->route('toupdateprofil');
                }
            }else{
                return redirect()->route('toupdateprofil');
            }
        }else{
            return redirect()->route('toupdateprofil');
        }

    }
}
