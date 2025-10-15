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
        // Définition des packages avec leur nombre maximum à récupérer
        $packageLimits = [
            'PKG_STR_SUPER_ELITE' => 100,
            'PKG_STR_ELITE' => 60,
            'PKG_STR_DIAMOND' => 40,
            'PKG_STR_EXTRA' => 20,
            'PKG_STR_SUPER' => 10,
            'PKG_STR_CLASSIC' => 10,
        ];

        $structures = [];

        foreach ($packageLimits as $packageCode => $limit) {

            // Récupération des boutiques liées à ce package
            $query = \DB::table('structures as str')
                ->join('souscription_structures as sstr', 'str.structure_matricule', '=', 'sstr.structure_matricule')
                ->join('package_structures as pstr', 'pstr.package_matricule', '=', 'sstr.package_matricule')
                ->where('sstr.package_matricule', $packageCode)
                ->where('sstr.statut', 'actif') // facultatif mais logique
                ->select(
                    'str.nom_structure as nom_structure',
                    'str.logo as logo',
                    'pstr.nom as package',
                    'str.structure_matricule as structure_matricule',
                    'str.sigle_structure as sigle'
                );

            // Récupère toutes les structures disponibles
            $structureList = $query->get();

            // Si le nombre de structures dépasse la limite, on coupe
            if ($structureList->count() > $limit) {
                $structureList = $structureList->take($limit);
            }

            // Ajoute au tableau global
            $structures[$packageCode] = $structureList;
        }

        return response()->json([
            'success' => true,
            'structures' => $structures,
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
