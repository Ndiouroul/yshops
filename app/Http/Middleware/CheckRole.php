<?php

namespace App\Http\Middleware;

use App\Models\User_role;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('connexion');
        }

        $user_fonction = Auth::user()->fonction;
        if ($user_fonction !== 'dashwood') {
            abort(403, 'Accès refusé.');
        }

        return $next($request);
    }
}
