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

class ShopController extends Controller
{
    /**
     * Affiche la vue de la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showShop(): View
    {
        return view('shop');
    }
    public function showShopDetails(Request $request, ...$param)
    {
        if(count($param) === 0){
            if (!Auth::check()) {
                return view('connexion'); // redirection Laravel
            }
            $user_matricule = Auth::user()->user_matricule;
            $role = User_role::where('user_matricule', $user_matricule)->first();
            $structure_matricule = $role->structure_matricule;
        }else{
            $structure_matricule = $param[0];
        }

        $structure = Structure::where('structure_matricule', $structure_matricule)
            ->get(['nom_structure', 'telephone1', 'email_structure', 'adresse_structure', 'sigle_structure', 'description', 'logo']);

        $products = Online_product::where('online_products.structure_matricule', $structure_matricule)
            ->join('all_products', 'online_products.product_matricule', '=', 'all_products.product_matricule')
            ->get([
                'all_products.nom_produit',
                'all_products.description',
                'all_products.product_matricule',
                'online_products.prix_vente',
                'online_products.quantite',
                'online_products.image',
            ]);

        return response()->json([
            'success' => true,
            'type'=> 'shopDetails',
            'message' => 'Détails de la boutique',
            'structure' => $structure,
            'products' => $products,
        ]);

    }

    public function validNewCommande(Request $request, ...$param){

        if (!Auth::check()) {
            return redirect('login'); // redirection Laravel
        }

        $index = $request->input('index');
        $productsList = []; // tableau pour stocker les produits valides

        if ($index !== null && $index > 0) {
            for ($i = 0; $i <= $index; $i++) {
                $matricule = $request->input("product_matricule".$i);
                $prix = $request->input("prix".$i);
                $quantite = $request->input("quantite".$i);
                $total = $request->input("total".$i);


                if (!empty($matricule)) {
                    $productsList[] = [
                        'matricule'        => $matricule,
                        'prix'        => $prix,
                        'quantite'        => $quantite,
                        'total'        => $total,
                    ];

                }
            }
        }

        if (empty($productsList)) {
            return response()->json([
                'success' => false,
                'type'    => 'noProduct',
                'message' => "Veuillez ajouter au moins un produit avant de soumettre la comande."
            ], 400);
        }

        $user_matricule = Auth::user()->user_matricule;
        $commande_matricule = 'commande-'.now()->format('YmdHis');

        foreach ($productsList as $product){
            $commande [] = [
                'commande_matricule' => $commande_matricule,
                'product_matricule' => $product['matricule'],
                'prix'=> $product['prix'],
                'quantite'=>$product['quantite'],
                'total'=>$product['total'],
                'user_matricule'=>$user_matricule,
            ];
        }

        Commande::insert($commande);

        return response()->json([
            'success' => true,
            'type'=> 'ValidCommandeRas',
            'message' => 'Commande validee et envoyeee',
        ]);

    }

}
