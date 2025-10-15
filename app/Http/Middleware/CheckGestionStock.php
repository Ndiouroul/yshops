<?php
namespace App\Http\Middleware;

use App\Models\Accreditation;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckGestionStock
{
    public function handle($request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect('connexion');
        }

        $user = Auth::user();
        $accreditation = Accreditation::where('agent_matricule', $user->user_matricule)->first();

        // Récupère la fonction
        $fonction = $user->fonction;

        // Vérifie la permission
        if (
            $fonction === 'proprietaire' ||
            (in_array($fonction, ['gerant', 'caissier']) && $accreditation->gestion_stock === 'ok')
        ) {
        return $next($request);
        }

        // Retourne 403 proprement
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return abort(403, 'Accès non autorisé');
    }
}
