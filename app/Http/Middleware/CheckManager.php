<?php

namespace App\Http\Middleware;

use App\Models\Accreditation;
use App\Models\Structure;
use App\Models\User_role;
use Closure;
use Illuminate\Support\Facades\Auth;
use function Illuminate\Foundation\Testing\Concerns\json;

class CheckManager
{
    public function handle($request, Closure $next)
    {
        $matricule = $request->route('matricule'); // récupéré depuis l’URL

        if (auth()->check()) {
            $user_matricule = Auth::user()->user_matricule;
            if(auth()->user()->fonction === 'proprietaire' ){
                $proof = User_role::where('user_matricule', $user_matricule)
                    ->where('structure_matricule', $matricule);
            }elseif(auth()->user()->fonction === 'gerant'){
                $proof = Accreditation::where('agent_matricule', $user_matricule)
                    ->where('structure_matricule', $matricule)
                    ->where('statut', 'actif');
            }
            if(!$proof){
                abort(403, "Accès non autorisè !");
            }
        }

        return $next($request);
    }


}
