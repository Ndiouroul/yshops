<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\All_product;
use App\Models\Commande;
use App\Models\Online_product;
use App\Models\Structure;
use App\Models\User_role;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

// Utilisez cette classe

class AccueilController extends Controller
{
    /**
     * Affiche la vue de la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function ShowAccueil()
    {
        return view('accueil');
    }
    public function getShops(Request $request)
    {
        $structure = Structure::all();
        return response()->json([
            'success' => true,
            'structure' => $structure,
        ]);
    }
    public function getOpenShops(Request $request, $matricule){
        $structure = Structure::where('structure_matricule', $matricule)
            ->first();

        return view('shopdetails', [
            'structure' => $structure
        ]);
    }

}
