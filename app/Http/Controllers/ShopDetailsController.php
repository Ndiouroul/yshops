<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Like;
use App\Models\All_product;
use App\Models\Commande;
use App\Models\Online_product;
use App\Models\Structure;
use App\Models\User_role;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use function Illuminate\Support\data;

// Utilisez cette classe

class ShopDetailsController extends Controller
{
    /**
     * Affiche la vue de la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showShop(): View
    {
        return view('shopdetails');
    }

    public function getOpenShopsDetails($param){

        $structure = Structure::where('structure_matricule', $param)->get();

        $products = Online_product::where('online_products.structure_matricule', $param)
            ->join('all_products', 'online_products.product_matricule', '=', 'all_products.product_matricule')
            ->selectRaw('
                all_products.nom_produit,
                all_products.description,
                all_products.product_matricule,
                MAX(online_products.prix_vente) as prix_vente,
                SUM(online_products.quantite) as quantite,
                MAX(online_products.image) as image
            ')
            ->groupBy(
                'all_products.nom_produit',
                'all_products.description',
                'all_products.product_matricule'
            )
            ->get();
        return response()->json([
            'success' => true,
            'type'=> 'shopDetails',
            'message' => 'Détails de la boutique',
            'products' => $products,
            'structure' => $structure,
        ]);
    }

    public function sabonner ($param){
        $user_matriule = Auth::user()->user_matricule;
        $abonnement_matricule = 'abonnement-'.now()->format('YmdHis');

        Abonnement::create([
            'abonnement_matricule' => $abonnement_matricule,
            'user_matricule' => $user_matriule,
            'structure_matricule' => $param,
        ]);

        return response()->json([
            'success' => true,
            'tupe' => 'abonnementRas',
            'message' => 'Abonneent effectue avec succes',
        ]);
    }

    public function voter($param)
    {
        $user_matriule = Auth::user()->user_matricule;
        $vote_matricule = 'vote-'.now()->format('YmdHis');

        Vote::create([
            'vote_matricule' => $vote_matricule,
            'user_matricule' => $user_matriule,
            'structure_matricule' => $param,
        ]);

        return response()->json([
            'success' => true,
            'tupe' => 'voterRas',
            'message' => 'Vote effectue avec succes',
        ]);
    }

    public function liker($param)
    {
        $user_matriule = Auth::user()->user_matricule;
        $like_matricule = 'like-'.now()->format('YmdHis');

        Like::create([
            'like_matricule' => $like_matricule,
            'user_matricule' => $user_matriule,
            'structure_matricule' => $param,
        ]);

        return response()->json([
            'success' => true,
            'tupe' => 'likeRas',
            'message' => 'Vous venez de liker cette boutique',
        ]);
    }

    public function getProductsShopsDetails(Request $request){
        $structure_matricule = $request->input('matricule');
        $chaine = $request->input('chaine');

        $products = $this->recolteProduitName($chaine, $structure_matricule);

        return response()->json([
            'success' => true,
            'tupe' => 'likeRas',
            'message' => 'Abonneent effectue avec succes',
            'data' => $products,
        ]);
    }

    protected function recolteProduitName($N, $S)
    {
        $collection = [];
        $products = All_product::where('nom_produit', 'like', "%{$N}%")
            ->where('structure_matricule', $S)
            ->get();

        foreach ($products as $product) {
            $existedProduct = Online_product::where('product_matricule', $product->product_matricule)
                ->where('quantite', '>', 0)
                ->get(['quantite', 'prix_vente', 'product_matricule'])
                ->unique('product_matricule');

            if ($existedProduct->isNotEmpty()) {
                foreach ($existedProduct as $item) {
                    $collection[] = [
                        'nom_produit' => $product->nom_produit,
                        'image' => $product->image,
                        'quantite'    => $item->quantite,
                        'prix_vente'  => $item->prix_vente,
                    ];
                }
            }
        }

        return $collection;
    }
}
