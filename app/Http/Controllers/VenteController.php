<?php

namespace App\Http\Controllers;

use App\Models\Accreditation;
use App\Models\All_product;
use App\Models\Fournisseur;
use App\Models\Online_product;
use App\Models\Structure;
use App\Models\Recue;
use App\Models\User_profile;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function Carbon\isEmpty;
use function League\Flysystem\type;
use function Ramsey\Collection\element;

// Utilisez cette classe

class VenteController extends Controller
{
    /**
     * Affiche la vue de la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showVente(): View
    {
        if (!Auth::check()) {
            return view('connexion'); // redirection si non connecté
        }

        return view('vente');
    }

    public function validCommande(Request $request)
    {
        $shopMatricule = $request->input("shopmatricule");
        $typeStructure = Structure::where('structure_matricule', $shopMatricule)
            ->get(['type_structure']);
       if ($typeStructure[0]['type_structure'] === 'revente'){
           $user_matricule = Auth::user()->user_matricule;
           $recue_matricule = 'recue-'.now()->format("YmdHis");
           $shopMatricule = $request->input('shopnameinput');
           $inputTotalGeneral = $request->input('inputtotalgeneral');
           $index = $request->input('index');
           $productsList = []; // tableau pour stocker les produits valides

           if ($index !== null && $index > 0) {
               for ($i = 0; $i <= $index; $i++) {
                   $nom = trim($request->input("nom".$i));
                   $quantite = trim($request->input("quantite".$i));
                   $total = trim($request->input("total".$i));
                   $prix = trim($request->input("prix_unitaire".$i));

                   $productInfos = All_product::where('nom_produit', $nom)->first();

                   if (!empty($nom)) {
                       foreach ($productsList as $items){
                           if($nom == $items['nom']){
                               $items['quantite'] += $quantite;
                               continue;
                           }
                       }
                       $productsList[] = [
                           'nom'        => $nom,
                           'quantite'   => $quantite,
                           'prix'   => $prix,
                           'total'   => $total,
                           'product_matricule' => $productInfos->product_matricule,
                       ];
                   }
               }
           }

           if (empty($productsList)) {
               return response()->json([
                   'success' => false,
                   'type'    => 'noProduct',
                   'message' => "Veuillez ajouter au moins un produit avant de soumettre la commande."
               ], 400);
           }

           $vente_matricule = 'vente-'.now()->format("YmdHis");

           foreach ($productsList as $product)
           {
               $onlineProductsQuantity = Online_product::where('product_matricule', $product['product_matricule'])
                   ->where('quantite', '>', 0)
                   ->sum('quantite');

               $onlineProductsQuantityByOne = Online_product::where('product_matricule', $product['product_matricule'])
                   ->where('quantite', '>', 0)
                   ->get();

               if($onlineProductsQuantity < $product['quantite'])
               {
                   return response()->json([
                       'success' => false,
                       'type' => 'stockKo',
                       'message' => 'Stock insuffisant. Restant: '.$onlineProductsQuantity. ' articles',
                   ]);
               }

               foreach ($onlineProductsQuantityByOne as $items2){
                   $quantiteSent = $product['quantite'];
                   if($product['quantite'] <= $items2->quantite){
                       $items2->quantite -= $product['quantite'];
                       $items2->save();
                       $product['quantite'] = 0;
                   }else{
                       $restant = $items2->quantite;
                       $items2->quantite = 0;
                       $items2->save();
                       $product['quantite'] -= $restant;
                   }

                   $element =[
                       'recue_matricule' => $recue_matricule,
                       'vente_matricule' => $vente_matricule,
                       'user_matricule' => $user_matricule,
                       'structure_matricule' => $shopMatricule,
                       'product_matricule' => $product['product_matricule'],
                       'prix' => $items2->prix_vente,
                       'quantite' => $quantiteSent,
                       'total' => $quantiteSent * $items2->prix_vente,
                       'created_at' => now(),
                       'updated_at' => now(),
                   ];
                   Recue::insert([$element]);

               }

           }

           Vente::create([
               'vente_matricule' => $vente_matricule,
               'prix_total'=> $inputTotalGeneral,
               'recue_matricule' => $recue_matricule,
               'structure_matricule' => $shopMatricule,
               'vendeur_matricule' => $user_matricule,
           ]);


           return response()->json([
               'success' => true,
               'type' => 'venteRas',
               'message' => 'Vente effectuee',
           ]);
       }elseif ($typeStructure[0]['type_structure'] === 'transformation'){
           $user_matricule = Auth::user()->user_matricule;
           $recue_matricule = 'recue-'.now()->format("YmdHis");
           $inputTotalGeneral = $request->input('inputtotalgeneral');
           $index = $request->input('index');
           $productsList = []; // tableau pour stocker les produits valides

           if ($index !== null && $index > 0) {
               for ($i = 0; $i <= $index; $i++) {
                   $nom = trim($request->input("nom".$i));
                   $quantite = trim($request->input("quantite".$i));
                   $prix_unitaire = trim($request->input("prix_unitaire".$i));

                   $productInfos = All_product::where('nom_produit', $nom)->first();

                   if(!empty($nom)){
                       $productsList[] = [
                           'nom' => $nom,
                           'quantite' => $quantite,
                           'prix_unitaire' => $prix_unitaire,
                           'product_matricule' => $productInfos->product_matricule,
                       ];
                   }
               }
           }

           if (empty($productsList)) {
               return response()->json([
                   'success' => false,
                   'type'    => 'noProduct',
                   'message' => "Veuillez ajouter au moins un produit avant de soumettre la commande."
               ], 400);
           }

           $vente_matricule = 'vente-'.now()->format("YmdHis");


           foreach ($productsList as $product)
           {
               $element =[
                   'recue_matricule' => $recue_matricule,
                   'vente_matricule' => $vente_matricule,
                   'user_matricule' => $user_matricule,
                   'structure_matricule' => $shopMatricule,
                   'product_matricule' => $product['product_matricule'],
                   'prix' => $product['prix_unitaire'],
                   'quantite' => $product['quantite'],
                   'total' => $product['quantite'] * $product['prix_unitaire'],
                   'created_at' => now(),
                   'updated_at' => now(),
               ];
               Recue::insert([$element]);
           }

           Vente::create([
               'vente_matricule' => $vente_matricule,
               'prix_total'=> $inputTotalGeneral,
               'recue_matricule' => $recue_matricule,
               'structure_matricule' => $shopMatricule,
               'vendeur_matricule' => $user_matricule,
           ]);

           return response()->json([
               'success' => true,
               'type' => 'venteRas',
               'message' => 'Vente effectuee',
           ]);
       }
    }

    public function getNewProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'identifiant' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifiants = trim($request->input('identifiant'));
        if(str_contains($identifiants, ' ')){
            $tabident = explode(' ', $identifiants);
            $nomCode = $tabident[0];
            $quantite = $tabident[1];
        }else{
            $quantite = null;
            $nomCode = $identifiants;
        }

        if ($quantite && $quantite=== 0)
        {
            return response()->json([
                'success' => false,
                'type'    =>'newProductSearched',
                'message' => 'Veuillez verifier vos entrees !',
            ]);
        }

        $user_matricule = Auth::user()->user_matricule;
        $shopMatricule = $request->input('shopMatricule');
        $structure = Structure::where('structure_matricule', $shopMatricule)->first();
        $typeStructure = $structure->type_structure;

        $product = All_product::where('code_barre', $nomCode)
            ->where('structure_matricule', $shopMatricule)
            ->first();

        if(!$product){
            if($typeStructure === 'revente'){
                $resultProduct = $this->recolteProduitName($nomCode, $shopMatricule);
            }elseif($typeStructure === 'transformation'){
                $resultProduct = $this->recolteProduitNameTransformation($nomCode, $shopMatricule);

            }
            if ($resultProduct)
            {
                return response()->json([
                    'success' => true,
                    'type'    =>'newProductSearchedName',
                    'message' => 'Produit trouve.',
                    'nom'    => $nomCode,
                    'data'   => $resultProduct,
                ]);

            }else
            {
                return response()->json([
                    'success' => false,
                    'type'    =>'newProductSearched',
                    'message' => 'Aucun produit trouve.',
                ]);
            }
        }

        if($typeStructure === 'reente'){
            $resultProduct = $this->recolteProduitCodeBarre($quantite, $product->product_matricule);
        }elseif ($typeStructure === 'transformation'){
            $resultProduct = $this->recolteProduitCodeBarreTransfornation($product->product_matricule);
        }

        if($resultProduct['statut'] === false){
            return response()->json([
                'success' => false,
                'type'    =>'newProductSearched',
                'message' => 'Stock insuffisant. Restant: '.$resultProduct['value']." articles disponibles",
//                'data'    => $nomCode." / ".$quantite,
            ]);
        }

        return response()->json([
            'success' => true,
            'type'    =>'newProductSearchedCodeBarre',
            'message' => 'Produit trouve.',
            'nom'    => $product->nom_produit,
            'data'   => $resultProduct['value'],
        ]);
    }

    public function getStructures(){
        $user_matricule = Auth::user()->user_matricule;
        $collection = null;

        $collection = Structure::where('user_matricule', $user_matricule)
            ->get(['nom_structure', 'structure_matricule', 'type_structure']);
        if (!$collection){
            $accreditations = Accreditation::where('agent_matricule', $user_matricule)->get(['structure_matricule']);
            foreach ($accreditations as $items){
                $structure = Structure::where('structure_matricule', $items)->get(['nom_structure', 'matricule_structure', 'type_structure']);
                $collection [] = $structure;
            }
        }

        return response()->json([
            "success" => true,
            'type' => 'getVenteStructure',
            'data' => $collection,
        ]);

    }

    protected function recolteProduitCodeBarre($Q,$P)
    {
        $totalQuantite = 0;
        $products = Online_product::where('product_matricule', $P)
            ->where('quantite', '>', 0)
            ->get(['quantite', 'prix_vente']);

        foreach ($products as $product)
        {
            $totalQuantite += $product->quantite;
        }
        if ($totalQuantite >= $Q)
        {
            return ['statut' => true, 'value' => $products];
        }
        else{
            return ['statut'=> false, 'value'=> $totalQuantite];
        }
    }
    protected function recolteProduitCodeBarreTransfornation($P)
    {
        $products = Online_product::where('product_matricule', $P)
            ->get(['prix_vente']);

        return ['statut' => true, 'value' => $products];
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
                ->get(['quantite', 'prix_vente', 'product_matricule']);

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
    protected function recolteProduitNameTransformation($N, $S)
    {
        $collection = [];
        $products = All_product::where('nom_produit', 'like', "%{$N}%")
            ->where('structure_matricule', $S)
            ->get();

        foreach ($products as $product) {
            $existedProduct = Online_product::where('product_matricule', $product->product_matricule)
                ->get(['prix_vente', 'product_matricule']);

            if ($existedProduct->isNotEmpty()) {
                foreach ($existedProduct as $item) {
                    $collection[] = [
                        'nom_produit' => $product->nom_produit,
                        'image' => $product->image,
                        'prix_vente'  => $item->prix_vente,
                    ];
                }
            }
        }

        return $collection;
    }


}
