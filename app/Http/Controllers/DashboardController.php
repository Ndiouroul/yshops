<?php

namespace App\Http\Controllers;
use App\Http\Requests\RequestAllProductUpdate;
use Illuminate\Support\Str;
use App\Http\Requests\RequestProductOnUpdate;
use App\Http\Requests\StoreNewBill;
use App\Http\Requests\StoreNewBillTrasformation;
use App\Http\Requests\StoreNewDealerRequest;
use App\Http\Requests\StoreNewManager;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StoreProfilRequest;
use App\Http\Requests\StoreShopRequest;
use App\Models\Accreditation;
use App\Models\All_product;
use App\Models\Depense;
use App\Models\Facture;
use App\Models\Fournisseur;
use App\Models\Ingredient;
use App\Models\Online_product;
use App\Models\Package_structure;
use App\Models\Recue;
use App\Models\Souscription_structure;
use App\Models\Structure;
use App\Models\User_profile;
use App\Models\Charge;
use App\Models\User;
use App\Models\User_role;
use App\Models\Vente;
use App\Models\Editing;
use Egulias\EmailValidator\Result\Reason\AtextAfterCFWS;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View; // Utilisez cette classe
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use function Carbon\isEmpty;
use function Illuminate\Process\input;
use function Illuminate\Support\data;
use function Illuminate\Validation\validated;
use function Ramsey\Collection\element;


class DashboardController extends Controller
{
    /**
     * Affiche la vue de la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showDashboard(): View
    {
        if (!Auth::check()) {
            return view('connexion'); // redirection Laravel
        }
        return view('dashboard');
    }

    public function addNewProduct(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $shopType = $request->input('shoptype');
        $user_matricule = Auth::user()->user_matricule;

        $structure_matricule = $request->input('shopmatricule');

        $codeBarre = $validated['code_barre'];
        $existsCodeBarre = All_product::where(function ($query) use ($codeBarre) {
            $query->where('code_barre', $codeBarre)
                ->orWhere('nom_produit', $codeBarre);
        })
            ->where('structure_matricule', $structure_matricule)
            ->first();

        if($existsCodeBarre){
            return response()->json([
                'success' => false,
                'type'    =>'codeBarreExists',
                'message' =>  'Code-barre indisponible. Veuillez en choisir un autre',
            ]);
        }

        $produit_matricule = 'produit-'.'-'.now()->format('YmdHis');
        $produit_matricule_online = 'produit-online-'.'-'.now()->format('YmdHis');

        // Gérer l'image si elle existe
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            // stocke dans storage/app/public/products
        }

        // Création du produit
        All_product::create([
            'description'          => $validated['description'],
            'product_matricule'    => $produit_matricule,
            'structure_matricule'  => $structure_matricule,
            'code_barre'           => $validated['code_barre'],
            'nom_produit'          => $validated['nom_produit'],
            'responsable_matricule'=> $user_matricule,
            'image'                => $imagePath,
        ]);
        if ($shopType === 'transformation'){
            Online_product::create([
                'product_matricule_online' => $produit_matricule_online,
                'product_matricule' => $produit_matricule,
                'structure_matricule' => $structure_matricule,
                'facture_matricule' => 'neant',
                'prix_achat' => 0,
                'prix_vente' => $request->input('prix'),
                'quantite' => 0,
                'marge' => 1,
                'image' => $imagePath,
            ]);
        }


        return response()->json([
            'success' => true,
            'type'    =>'newProduct',
            'message' => 'Produit créé avec succès !',
        ]);
    }

    public function addNewStructure(StoreShopRequest $request)
    {
        $check_telephone1 = $this->checkExists($request->input('telephone1'), 'telephone1', 'structures');
        $check_telephone2 = $this->checkExists($request->input('telephone2'), 'telephone2', 'structures');
        $check_email = $this->checkExists($request->input('email_structure'), 'email_structure', 'structures');
        $telephone_fixe = $this->checkExists($request->input('telephone_fixe'), 'telephone_fixe', 'structures');
        $collection = ['Le telephone principal'=>$check_telephone1, 'Le telephone secondaire' => $check_telephone2, "L'email" => $check_email, 'Le telephone fixe' => $telephone_fixe];

        foreach ($collection as $texte => $check){
            if($check){
                return response()->json([
                   'success'=> false,
                   'type'=> 'errorDuplicata',
                   'message' => "{$texte} est déjà associé à un compte existant.",
                ]);
            }
        }

        $validated = $request->validated();
        $structure_matricule = 'structure-'.Str::uuid().now()->format('YmdHis');

//        $accreditation_matricule = 'accreditation-'.$validated['sigle_structure']."-".now()->format('YdmHis');
        $role_matricule = 'role-proprietaire-'.Str::uuid()."-".now()->format('YmdHis');
        $user_matricule = Auth::user()->user_matricule;

        $existingRole = User_profile::where('user_matricule', $user_matricule)
            ->where('profil', '!=', 'vendeur')
            ->where('profil', '!=', 'mixte')
            ->first();
        if ($existingRole) {
            return response()->json([
                'success' => false,
                'type' => 'toDisplay',
                'message' => 'Veuillez mettre à jour votre profil avant de créer une nouvelle boutique',
            ]);
        }

        $existAccreditations = Accreditation::where('agent_matricule', $user_matricule)->first();
        if($existAccreditations){
            return response()->json([
                'success' => false,
                'type' => 'toDisplay',
                'message' => 'Votre compte est déjà affilié en tant que '.$existAccreditations->role.' à une autre structure. Vous ne pouvez pas créer une nouvelle boutique avec ce compte.
'
            ]);
        }

        $nombreBoutique = Structure::where('user_matricule', $user_matricule)
            ->count();

        $old_souscription = Souscription_structure::where('responsable_matricule', $user_matricule)
            ->where('statut', 'actif')
            ->get();

        $cumulBoutique = 0;
        foreach ($old_souscription as $souscription){
            $packages = Package_structure::where('package_matricule', $souscription->package_matricule)->first();
            $cumulBoutique += $packages -> nombre_boutique;
        }

        if ($nombreBoutique >= $cumulBoutique && $nombreBoutique >0){
            return response()->json([
                'success' => false,
                'type' => 'toDisplay',
                'message' => "Votre package actuel ne vous permet la creation d'une nouvelle boutique.",
            ]);
        }

        // 👇 Traitement du logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        Structure::create([
            'user_matricule' => $user_matricule,
            'structure_matricule' => $structure_matricule,
            'type_structure' => $validated['type_structure'],
            'nom_structure' => $validated['nom_structure'],
            'sigle_structure' => $validated['sigle_structure'],
            'logo' => $logoPath, // 👈 on enregistre juste le chemin relatif
            'telephone1' => $validated['telephone1'],
            'telephone2' => $validated['telephone2'] ?? null,
            'telephone_fixe' => $validated['telephone_fixe'] ?? null,
            'adresse_structure' => $validated['adresse_structure'],
            'description' => $validated['description'],
            'email_structure' => $validated['email_structure'],
        ]);

        User_role::create([
            'role_matricule' => $role_matricule,
            'user_matricule' => $user_matricule,
            'responsable_matricule' => $user_matricule,
            'structure_matricule' => $structure_matricule,
            'fonction' => 'proprietaire',
            'gestion_stock' => 'ok',
        ]);

        $souscription_matricule = 'souscription-'.Str::uuid()."-".now()->format('YmdHis');

        Souscription_structure::create([
            'souscription_matricule' => $souscription_matricule,
            'structure_matricule' => $structure_matricule,
            'responsable_matricule' => $user_matricule,
            'created_at' => now(),
            'package_matricule' => 'PKG_STR_CLASSIC',
        ]);

        $user = Auth::user();
        $user->update([
            'fonction' => 'proprietaire',
        ]);

        return response()->json([
            'success' => true,
            'type' => 'newShop',
            'message' => 'Boutique créée avec succès !',
        ]);


    }

    public function showProfil()
    {
        $user_matricule = Auth::user()->user_matricule;
        $user_profile = User_profile::where('user_matricule', $user_matricule)->first();

        return response()->json($user_profile);
    }

    public function check_password(Request $request)
    {
        $request->validate(
            [
                'password' => 'required|string|min:7',
            ],
            [
                'password.required' => '⚠️ Le mot de passe est obligatoire.',
                'password.min'      => '⚠️ Le mot de passe doit contenir au moins 7 caractères.',
                'password.string'   => '⚠️ Le mot de passe doit être une chaîne de caractères.',
            ]
        );

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            // ✅ Mot de passe correct
            return response()->json(['success' => true, 'type' =>"checkPassword", 'message' => 'Mot de passe vérifié.']);
        } else {
            // ❌ Mauvais mot de passe
            return response()->json(['success' => false, 'type' => 'checkPassword', 'message' => 'Mot de passe incorrect.']);
        }

    }

    public function updateProfil(StoreProfilRequest $request)
    {
        // Récupère le profil de l'utilisateur connecté
        $user_matricule = Auth::user()->user_matricule;
        $user_profile = User_profile::where('user_matricule', $user_matricule)->first(); // ok même si clé primaire = id


        // Données validées
        $validated = $request->validated();

        // Gestion de la photo si un fichier est envoyé
        if ($request->hasFile('photo_profil')) {
            $file = $request->file('photo_profil');

            // Nom unique
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();


            // Stockage dans storage/app/public/profiles
            $path = $file->storeAs('profiles', $fileName, 'public');

            // Supprime l'ancienne photo si elle existe
            if ($user_profile->photo_profil && Storage::disk('public')->exists($user_profile->photo_profil)) {
                Storage::disk('public')->delete($user_profile->photo_profil);
            }

            // Met à jour le chemin de la photo dans les données validées
            $validated['photo_profil'] = $path;
        }

        // Mise à jour du profil
        $user_profile->update($validated);


        return response()->json([
            'success' => true,
            'type' => 'updateProfil',
            'message' => 'Profil mis à jour avec succès !',
            'data' => $user_profile
        ]);
    }

    public function addNewManager(StoreNewManager $request)
    {
        $user_matricule = Auth::user()->user_matricule;
        $shopmatricule = $request->input('shopmatricule');

        $validated = $request->validated();
        $accreditation_matricule = 'accreditation-'.$validated['pseudo'].'-'.now()->format('YmdHis');
        $user_matricule_gerant = 'user-'.$validated['pseudo'].'-'.now()->format('YmdHis');
        $profile_matricule = 'profil-'.$validated['pseudo'].'-'.now()->format('YmdHis');
        User::create([
            'user_matricule' => $user_matricule_gerant,
            'pseudo' => $validated['pseudo'],
            'email' => $validated['email'],
            'telephone1' => $validated['telephone1'],
            'password' => $validated['password'],
            'fonction' => 'gerant',
        ]);

        Accreditation::create([
            'accreditation_matricule' => $accreditation_matricule,
            'structure_matricule' => $shopmatricule,
            'agent_matricule' => $user_matricule_gerant,
            'responsable_matricule' => $user_matricule,
            'role' => 'gerant',
            'gestion_stock' => 'ok',
        ]);

        User_profile::create([
            'profile_matricule' => $profile_matricule,
            'user_matricule' => $user_matricule_gerant,
            'telephone1' => $request->input('telephone1'),
            'email' => $request->input('email'),
            'nom' => '',
            'prenom' => '',
            'adresse' => '',
            'profil' => 'simple-user',
            'photo_profil' => '',
        ]);

        return response()->json([
            'success' => true,
            'type' => 'newManager',
            'message' => 'Noueau gerant ajoute avec succes',
        ]);
    }

    public function addNewSeller(StoreNewManager $request)
    {
        $user_matricule = Auth::user()->user_matricule;
        $shopmatricule = $request->input('shopmatricule');

        $validated = $request->validated();
        $accreditation_matricule = 'accreditation-'.$validated['pseudo'].'-'.now()->format('YmdHis');
        $user_matricule_seller = 'user-'.$validated['pseudo'].'-'.now()->format('YmdHis');
        $profile_matricule = 'profil-'.$validated['pseudo'].'-'.now()->format('YmdHis');
        User::create([
            'user_matricule' => $user_matricule_seller,
            'pseudo' => $validated['pseudo'],
            'email' => $validated['email'],
            'telephone1' => $validated['telephone1'],
            'password' => $validated['password'],
            'fonction' => 'caissier'
        ]);

        Accreditation::create([
            'accreditation_matricule' => $accreditation_matricule,
            'structure_matricule' => $shopmatricule,
            'agent_matricule' => $user_matricule_seller,
            'responsable_matricule' => $user_matricule,
            'role' => 'caissier',
        ]);

        User_profile::create([
            'profile_matricule' => $profile_matricule,
            'user_matricule' => $user_matricule_seller,
            'telephone1' => $validated['telephone1'],
            'email' => $validated['email'],
            'nom' => '',
            'prenom' => '',
            'adresse' => '',
            'profil' => 'simple-user',
            'photo_profil' => '',
        ]);


        return response()->json([
            'success' => true,
            'type' => 'newManager',
            'message' => 'Noueau vendeur ajoute avec succes',
        ]);
    }

    public function showShop(Request $request)
    {
        $user_matricule = Auth::user()->user_matricule;
        $fonction = Auth::user()->fonction;
        $collection = [];
        if($fonction === 'proprietaire'){
            $shops = Structure::where('user_matricule', $user_matricule)
                ->get();
            foreach ($shops as $shop){
                $user_shops = Structure::where('structure_matricule', $shop->structure_matricule)
                    ->get(['nom_structure', 'logo', 'structure_matricule', 'type_structure']);
                $collection [] = $user_shops;
            }
        }else{
            $shops = Accreditation::where('agent_matricule', $user_matricule)
                ->where('gestion_stock', 'ok')
                ->where('statut', 'actif')
                ->distinct()
                ->get(['structure_matricule']);
            foreach ($shops as $shop){
                $user_shops = Structure::where('structure_matricule', $shop->structure_matricule)
                    ->get(['nom_structure', 'logo', 'structure_matricule', 'type_structure']);
                $collection [] = $user_shops;
            }
        }

        if (count($collection) === 0) {
            return response()->json([
                'success' => false,
                'type'    => 'sendShop',
                'message' => 'Aucune boutique trouvée pour cet utilisateur',
                'data'    => []
            ], 200);
        }

        return response()->json([
            'success' => true,
            'type'    => 'sendShop',
            'message' => 'Liste des boutiques',
            'data'    => $collection
        ], 200);

    }

    public function addNewDealer(StoreNewDealerRequest $request)
    {
        $user_matricule = Auth::user()->user_matricule;
        $nom_fournisseur = $request->input('nom_fournisseur');
        $founisseur_matricule = 'fournisseur-'.$nom_fournisseur.now()->format('YmdHis');
        $structure_matricule = $request->input('shopmatricule');

        $validated = $request -> validated();

        $path = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
        }

        Fournisseur::create([
            'fournisseur_matricule' => $founisseur_matricule,
            'structure_matricule'   => $structure_matricule,
            'responsable_matricule'   => $user_matricule,
            'nom_fournisseur'       => $validated['nom_fournisseur'],
            'sigle_fournisseur'     => $validated['sigle_fournisseur'] ?? null,
            'adresse'               => $validated['adresse'] ?? null,
            'telephone1'              => $validated['telephone1'] ?? null,
            'telephone2'              => $validated['telephone2'] ?? null,
            'telephone_fixe'          => $validated['telephone_fixe'] ?? null,
            'logo'                  => $path, // ✅ utiliser $path
            'email'                 => $validated['email'] ?? null,
            'description'           => $validated['description'] ?? null,
            'nom_agent'             => $validated['nom-_agent'] ?? null,
            'telephone_agent'          => $validated['telephone_agent'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'type'    =>'newDealer',
            'message' => 'Fournisseur créé avec succès !',
        ]);
    }

    public function addNewBill(StoreNewBill $request)
    {
        $index = $request->input('index-facture');
        $productsList = []; // tableau pour stocker les produits valides

        if ($index !== null && $index > 0) {
            for ($i = 0; $i <= $index; $i++) {
                $nom = $request->input("nom_produit".$i);

                if (!empty($nom)) {
                    $productsList[] = [
                        'nom'        => $nom,
                    ];
                }
            }
        }

        if (empty($productsList)) {
            return response()->json([
                'success' => false,
                'type'    => 'noProduct',
                'message' => "Veuillez ajouter au moins un produit avant de soumettre la facture."
            ], 400);
        }


        $validated = $request->validated();
        $user_matricule = Auth::user()->user_matricule;
        $fournisseur_matricule = $request->input('fournisseur');
        $facture_number = $request->input('numero_facture');
        $marge                 = $validated['marge'];

        $fournisseur = Fournisseur::where('fournisseur_matricule', $fournisseur_matricule)->first();
        $structure_matricule = $fournisseur->structure_matricule;
        $souscription = Souscription_structure::where('structure_matricule', $structure_matricule)
            ->where('statut', 'actif')
            ->get();
        $nombrePhotoAutorise = 0;
        foreach ($souscription as $element){
            $packages = Package_structure::where('package_matricule', $element->package_matricule)->first();
            $nombrePhotoAutorise += $packages->nombre_image_produit;
        }


        if (!$fournisseur) {
            return response()->json([
                'success' => false,
                'type'    => 'noDealer',
                'message' => "Ce fournisseur n'est pas encore répertorié.\r\nVeuillez l'ajouter pour valider cette facture."
            ], 404);
        }

        $facture_matricule = 'facture-' . $facture_number .'-'. now()->format("YmdHis");
        $product_matricule_online = 'produit-online-'. now()->format("YmdHis");

        $facturePath = null;
        if ($request->hasFile('facture')) {
            $facturePath = $request->file('facture')->store('factures', 'public');
        }

        Facture::create([
            'facture_matricule'     => $facture_matricule,
            'numero_facture'        => $validated['numero_facture'],
            'fournisseur'           => $validated['fournisseur'],
            'marge'                 => $validated['marge'],
            'facture'               => $facturePath, // ✅ chemin enregistré, pas l’objet
            'structure_matricule'   => $fournisseur->structure_matricule,
            'fournisseur_matricule' => $fournisseur->fournisseur_matricule,
            'responsable_matricule' => $user_matricule,
            'total_facture'         => $validated['total_facture'],
            'image',
        ]);

        $produits = [];

        $index = $request->input('index-facture');

        if ($index !== null && $index >= 0) {
            for ($i = 0; $i <= $index; $i++) {
                $nomProduit       = $request->input("nom_produit{$i}");
                $product_matricule= $request->input("product_matricule{$i}");
                $codebarre        = $request->input("code_barre{$i}");
                $prixAchat     = $request->input("prix_unitaire{$i}");
                $prixVente     = $marge * $request->input("prix_unitaire{$i}");
                $quantite         = $request->input("quantite{$i}");

                if (!empty($nomProduit)) {
                    // Gestion des images liées au produit
                    $inputName = "input-add-images-multiple{$i}";
                    $imagesPaths = [];

                    if ($request->hasFile($inputName)) {
                        foreach ($request->file($inputName) as $image) {
                            if ($image->isValid()) {
                                $path = $image->store('produits', 'public');
                                $imagesPaths[] = $path; // On stocke juste le chemin
                                if (count($imagesPaths) > $nombrePhotoAutorise){
                                    return response()->json([
                                        'success' => false,
                                        'type'    => 'newBillphoto',
                                        'message' => "Votre package actuel ne vous permet que ".$nombrePhotoAutorise." photos par produit.",
                                    ], 404);
                                }
                            }
                        }
                    }else{
                        return response()->json([
                            'success' => false,
                            'type'    => 'newBill',
                            'message' => "Chaque  produit doit avoir au moins 1 image."
                        ], 404);
                    }

                    // On transforme le tableau en une string séparée par virgules
                    $imagesString = implode(',', $imagesPaths);

                    // Préparation du produit
                    $produits[] = [
                        'product_matricule_online'    => $product_matricule_online,
                        'product_matricule'    => $product_matricule,
                        'structure_matricule'  => $structure_matricule,
                        'facture_matricule'    => $facture_matricule,
                        'prix_achat'        => $prixAchat,
                        'prix_vente'             => $prixVente,
                        'marge'             => $marge,
                        'quantite'             => $quantite,
                        'image'                => $imagesString, // ✅ toutes les images séparées par virgule
                    ];
                }
            }
        }

        if (count($produits) > 0) {
            Online_product::insert($produits);
        }


        return response()->json([
            'success' => true,
            'type'    => 'newBill',
            'message' => "Facture sauvegardée avec succès."
        ], 201);
    }

    public function addNewBillTransformationStrucutre(StoreNewBillTrasformation $request)
    {
        $index = $request->input('index-facture');
        $shopMatricule = $request->input('shopmatricule');
        $productsList = []; // tableau pour stocker les produits valides

        if ($index !== null && $index > 0) {
            for ($i = 0; $i <= $index; $i++) {
                $ingredientMatricule = $request->input("ingredient_matricule".$i);
                $ingredientNom = $request->input("nom_ingredient".$i);
                $ingredientQuantite = $request->input("quantite".$i);
                $ingredientPrix = $request->input("prix_unitaire".$i);

                if (!empty($ingredientMatricule) && !empty($ingredientNom) && !empty($ingredientQuantite) && !empty($ingredientPrix) ) {
                    $productsList[] = [
                        'ingredient_matricule' => $ingredientMatricule,
                        'quantite' => $ingredientQuantite,
                        'prix' => $ingredientPrix,
                        'total' => $ingredientQuantite * $ingredientPrix,
                    ];
                }
            }
        }

        if (empty($productsList)) {
            return response()->json([
                'success' => false,
                'type'    => 'noProduct',
                'message' => "Veuillez ajouter au moins une depense avant de soumettre la facture."
            ], 400);
        }


        $validated = $request->validated();
        $user_matricule = Auth::user()->user_matricule;
        $charge_number = $request->input('numero_facture');


        $charge_matricule = 'charge-' . $charge_number .'-'. now()->format("YmdHis");
        $depense_matricule = 'depense-' . $charge_number .'-'. now()->format("YmdHis");

        $facturePath = null;
        if ($request->hasFile('facture')) {
            $facturePath = $request->file('facture')->store('factures', 'public');
        }

        Charge::create([
            'charge_matricule'     => $charge_matricule,
            'numero_facture'        => $validated['numero_facture'],
            'facture'               => $facturePath,
            'responsable_matricule' => $user_matricule,
            'structure_matricule' => $shopMatricule,
            'total_facture'         => $validated['total_facture'],
        ]);

        foreach ($productsList as $product){
            $product['charge_matricule'] = $charge_matricule;
            $product['depense_matricule'] = $depense_matricule;
            $product['responsable_matricule'] = $user_matricule;
            $product['structure_matricule'] = $shopMatricule;
            $product['created_at'] = now();
            $product['updated_at'] = now();

            Depense::insert($product);
        }

        return response()->json([
            'success' => true,
            'type'    => 'newBillTransform',
            'message' => "Facture sauvegardée avec succès.",
            'data' => $index,
        ], 201);
    }

    public function getbillproducts(Request $request, $param){
        $shopmatricule = $param;
        $structure = Structure::where('structure_matricule', $shopmatricule)->first();
        $shopType = $structure->type_structure;
        $shopCategorie = $structure->categorie_structure;

        if ($shopType === 'revente'){
            $products = All_product::where('structure_matricule', $shopmatricule)->get();
        }elseif ($shopType === 'transformation'){
            $products = Ingredient::where('categorie', $shopCategorie)->get();
        }

        return response()->json([
            'success' => true,
            'type' => 'billProducts',
            'message' => $products->isEmpty() ? 'Aucun produit trouvé' : 'Liste des produits',
            'data' => $products,
            'matricule'=>$shopmatricule,
            'shop_type' => $shopType,
        ]);
    }

    public function getDealerList(Request $request, $param)
    {
        $shopmatricule = $param;

        // Récupérer les produits associés
        $fournisseurs = Fournisseur::where('structure_matricule', $shopmatricule)->get(['nom_fournisseur', 'fournisseur_matricule']);

        return response()->json([
            'success' => true,
            'type' => 'billProducts',
            'message' => $fournisseurs->isEmpty() ? 'Aucun fournisseur trouvé' : 'Liste des fournisseurs',
            'data' => $fournisseurs,
            'matricule'=>$shopmatricule
        ]);
    }

    public function getStockShops()
    {
        $user_matricule = Auth::user()->user_matricule;

        $user_shops = User_role::where('user_matricule', $user_matricule)
            ->where('gestion_stock', 'ok')
            ->distinct()
            ->pluck('structure_matricule');

        if ($user_shops->isEmpty()) {
            return response()->json([
                'success' => false,
                'type'    => 'sendShop',
                'message' => 'Aucune boutique trouvée pour cet utilisateur',
                'data'    => []
            ], 200);
        }

        $shop_names = Structure::whereIn('structure_matricule', $user_shops)
            ->get(['nom_structure', 'logo']);

        return response()->json([
            'success' => true,
            'type'    => 'sendShop',
            'message' => 'Liste des boutiques',
            'data'    => $shop_names
        ], 200);
    }

    public function getStockProducts($param)
    {
        $user_matricule = Auth::user()->user_matricule;

// Récupérer les boutiques de l'utilisateur avec gestion_stock
        $user_shops = User_role::where('user_matricule', $user_matricule)
            ->where('gestion_stock', 'ok')
            ->distinct()
            ->pluck('structure_matricule');

        foreach ($user_shops as $shop) {
            $structure = Structure::where('structure_matricule', $shop)
                ->where('nom_structure', $param)
                ->first();

            if ($structure) {
                // Récupérer les produits en ligne
                $onlineProducts = Online_product::where('structure_matricule', $shop)
                    ->get(['product_matricule', 'prix_vente', 'image', 'quantite']);

                // Récupérer les produits "all_products"
                $allProducts = All_product::where('structure_matricule', $shop)
                    ->get(['product_matricule', 'nom_produit', 'description']);

                // Transformer allProducts en tableau associatif pour un accès rapide
                $allProductsMap = $allProducts->keyBy('product_matricule');

                // Fusionner les données par product_matricule
                $merged = $onlineProducts->map(function($online) use ($allProductsMap) {
                    $productMatricule = $online->product_matricule;

                    $all = $allProductsMap->get($productMatricule);

                    return [
                        'product_matricule' => $productMatricule,
                        'prix_vente'        => $online->prix_vente,
                        'image'             => $online->image,
                        'nom_produit'       => $all->nom_produit ?? null,
                        'quantite'       => $online->quantite,
                        'description'       => $all->description ?? null,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'type'    => 'sendShop',
                    'message' => 'Liste des produits de la boutique',
                    'data'    => $merged,
                ]);
            }
        }


        if ($user_shops->isEmpty()) {
            return response()->json([
                'success' => false,
                'type'    => 'sendShop',
                'message' => 'Aucune boutique trouvée pour cet utilisateur',
                'data'    => []
            ], 200);
        }
    }

    public function gettingEverything($matricule, $thing){
        $typeStructure = null;
        $dataProduitAll = null;
        $dataProduitOn = null;
        $data = null;
        $structure = Structure::where('structure_matricule', $matricule)->first();
        $typeStructure = $structure->type_structure;

        if ($thing === "produits"){
            $dataProduitAll = \DB::table('all_products as ap')
                ->where('ap.structure_matricule', $matricule)
                ->join('user_profiles as up', 'up.user_matricule', '=', 'ap.responsable_matricule')
                ->select('ap.nom_produit as nom_produit',
                    'ap.code_barre as code_barre', 'ap.created_at as date_creation',
                    'up.prenom as prenom_responsable', 'up.nom as nom_responsable', 'ap.product_matricule'
                )
                ->get();
            $dataProduitOn = \DB::table('online_products as op')
                ->join('all_products as ap', 'ap.product_matricule', '=', 'op.product_matricule')
                ->join('factures as ftr', 'op.facture_matricule', '=', 'ftr.facture_matricule')
                ->join('user_profiles as up', 'up.user_matricule', '=', 'ftr.responsable_matricule')
                ->where('ap.structure_matricule', $matricule)
                ->select('op.prix_achat as prix_achat', 'ap.nom_produit as nom_produit', 'op.created_at as date_creation',
                    'op.prix_vente as prix_vente', 'up.prenom as prenom_responsable', 'up.nom as nom_responsable', 'op.product_matricule'
                )
                ->get();

        }elseif($thing === 'factures'){
            if ($typeStructure === 'revente'){
                $data = \DB::table('factures as ftr')
                    ->join('fournisseurs as fsr', 'fsr.fournisseur_matricule', '=', 'ftr.fournisseur_matricule')
                    ->join('user_profiles as up', 'ftr.responsable_matricule', '=', 'up.user_matricule')
                    ->join('users as u', 'u.user_matricule', '=', 'ftr.responsable_matricule')
                    ->select('ftr.numero_facture as numero', 'ftr.total_facture as total_facture', 'ftr.facture as facture', 'fsr.nom_fournisseur as nom_fournisseur'
                        , 'up.prenom as prenom_responsable', 'up.nom as nom_responsable',  'ftr.created_at as date_ajoute', 'ftr.facture_matricule')
                    ->where('ftr.structure_matricule', $matricule)
                    ->get();
            }elseif($typeStructure === 'transformation'){
                $data = \DB::table('charges as crg')
                    ->join('user_profiles as up', 'crg.responsable_matricule', '=', 'up.user_matricule')
                    ->join('users as u', 'u.user_matricule', '=', 'crg.responsable_matricule')
                    ->select('crg.numero_facture as numero', 'crg.total_facture as total_facture', 'crg.facture as facture'
                        , 'up.prenom as prenom_responsable', 'up.nom as nom_responsable',  'crg.created_at as date_ajoute', 'crg.charge_matricule')
                    ->where('crg.structure_matricule', $matricule)
                    ->get();
            }
        }elseif($thing === 'fournisseurs'){
            $data = \DB::table('fournisseurs as fsr')
                ->join('user_profiles as up', 'fsr.responsable_matricule', '=', 'up.user_matricule')
                ->join('users as u', 'fsr.responsable_matricule', '=', 'u.user_matricule')
                ->select('fsr.nom_fournisseur', 'fsr.telephone1 as tel1', 'fsr.email as email',
                    'fsr.statut as statut', 'fsr.fournisseur_matricule')
                ->where('fsr.structure_matricule', $matricule)
                ->get();
        }elseif ($thing === 'vendeurs') {
            $data = \DB::table('accreditations as acr')
                ->join('user_profiles as up', 'acr.agent_matricule', '=', 'up.user_matricule') // Agent
                ->join('users as u', 'acr.agent_matricule', '=', 'u.user_matricule') // Infos agent
                ->where('acr.structure_matricule', $matricule)
                ->where('u.fonction', 'caissier') // role sur l’agent
                ->select('up.prenom as agent_prenom', 'up.nom as agent_nom', 'up.telephone1 as agent_tel1',
                    'acr.privilege as privilege', 'u.user_matricule', 'acr.statut as statut'
                )
                ->get();
        }
        elseif ($thing === 'gerants') {
            $data = \DB::table('accreditations as acr')
                ->join('user_profiles as up', 'acr.agent_matricule', '=', 'up.user_matricule') // Agent
                ->join('users as u', 'acr.agent_matricule', '=', 'u.user_matricule') // Infos agent
                ->where('acr.structure_matricule', $matricule)
                ->where('u.fonction', 'gerant') // role sur l’agent
                ->select('up.prenom as agent_prenom', 'up.nom as agent_nom', 'up.telephone1 as agent_tel1',
                    'acr.privilege as privilege', 'u.user_matricule', 'acr.statut as statut'
                )
                ->get();
        }



        return response()->json([
            'success' => true,
            'type' => 'gettingEverythingOk',
            'type_structure' => $typeStructure,
            'data' => $data,
            'dataAll' => $dataProduitAll,
            'dataOn' => $dataProduitOn,

        ]);
    }

    public function gettingSoldData($param)
    {
        // Charger une map product_matricule => nom_produit (tous produits)
        $allProducts = All_product::pluck('nom_produit', 'product_matricule')->toArray();

        // Récupérer toutes les ventes de la structure
        $datasVente = Recue::where('structure_matricule', $param)
            ->get(['structure_matricule', 'prix', 'quantite', 'total', 'created_at', 'product_matricule']);

        // Ajouter le nom du produit à chaque entrée
        $soldData = $datasVente->map(function ($recueItem) use ($allProducts) {
            return [
                'structure_matricule' => $recueItem->structure_matricule,
                'prix' => $recueItem->prix,
                'quantite' => $recueItem->quantite,
                'total' => $recueItem->total,
                'created_at' => $recueItem->created_at,
                'product_matricule' => $recueItem->product_matricule,
                'nom_produit' => $allProducts[$recueItem->product_matricule] ?? 'Produit inconnu',
            ];
        });

        return response()->json([
            'message' => "Données des ventes",
            'data' => $soldData,
        ]);
    }

    public function gettingSoldFeesData($param){

        $structure = Structure::where('structure_matricule', $param)->first();
        $type_structure = $structure->type_structure;
        if($type_structure === 'revente'){
            $fees = Facture::where('structure_matricule', $param)->get(['total_facture', 'created_at']);
        }elseif ($type_structure === 'transformation'){
            $fees = Charge::where('structure_matricule', $param)->get(['total_facture', 'created_at']);
        }
        $solds = Recue::where('structure_matricule', $param)->get(['total', 'created_at']);

        return response()->json([
            'message' => "Données des ventes et depenses",
            'fees' => $fees,
            'solds' => $solds,
        ]);
    }

    public function gettingElementForUpdating($element, $matricule){
        $shops = null;
        $data = "Aucune occurence trouvee";
        if ($element === "getProductsAll"){
            $data = All_product::where("product_matricule", $matricule)->first();
        }
        elseif ($element === "getsProductsOn"){
            $data = \DB::table('online_products as op')
                ->join('all_products as ap', 'op.product_matricule', '=', 'ap.product_matricule')
                ->join('factures as ftr', 'ftr.facture_matricule', '=', 'op.facture_matricule')
                ->where('op.product_matricule', $matricule)
                ->where('quantite', '>', 0)
                ->select('ap.nom_produit as nom', 'ftr.facture as facture', 'ftr.numero_facture as numero_facture',
                    'op.image as images', 'op.prix_achat', 'op.prix_vente', 'op.product_matricule as product_matricule',
                        'op.marge', 'op.statut as statut', 'op.date_peremption as promption')
                ->get();
        }
        elseif ($element === 'getsBill'){
            if (str_contains($matricule, 'facture')){
                $facture = Facture::where("facture_matricule", $matricule)->first();
                $structure_matricule = $facture->structure_matricule;
            }elseif (str_contains($matricule, 'charge')){
                $facture = Charge::where("charge_matricule", $matricule)->first();
                $structure_matricule = $facture->structure_matricule;
            }

            $structure = Structure::where('structure_matricule', $structure_matricule)->first();
            if($structure->type_structure === "revente"){
                $data = \DB::table('factures as ftr')
                    ->join('structures as str', 'str.structure_matricule', '=', 'ftr.structure_matricule')
                    ->join('fournisseurs as fsr', 'fsr.fournisseur_matricule', '=', 'ftr.fournisseur_matricule')
                    ->select('ftr.numero_facture as numero', 'ftr.facture as facture', 'ftr.marge as marge',
                        'fsr.nom_fournisseur as nom_fournisseur', 'ftr.total_facture as total_facture', 'str.type_structure as type_structure',
                        'ftr.created_at as date_ajout', 'str.structure_matricule as structure_matricule')
                    ->where('ftr.facture_matricule', $matricule)
                    ->get();
            }
            elseif ($structure->type_structure === "transformation"){
                $data = \DB::table('charges as cgr')
                    ->join('structures as str', 'str.structure_matricule', '=', 'cgr.structure_matricule')
                    ->select('cgr.numero_facture as numero', 'cgr.facture as facture',
                        'cgr.total_facture as total_facture', 'str.type_structure as type_structure', 'cgr.created_at as date_ajout')
                    ->where('cgr.charge_matricule', $matricule)
                    ->get();
            }
        }
        elseif ($element === 'getsDealer'){
            $data = Fournisseur::where('fournisseur_matricule', $matricule)->first();
        }
        elseif($element === 'getsEmployes'){
            $accreditation = Accreditation::where('agent_matricule', $matricule)->first();
            $structure_matricule = $accreditation->structure_matricule;
            $structure = Structure::where('structure_matricule', $structure_matricule)->first();
            $user_matricule_proprio = $structure->user_matricule;

            $shops = \DB::table('structures as str')
                ->select('str.nom_structure as nom_structure', 'str.structure_matricule as structure_matricule')
                ->where('user_matricule', $user_matricule_proprio)
                ->get();

            $data = \DB::table('accreditations as acr')
                ->join('user_profiles as up', 'acr.agent_matricule', '=', 'up.user_matricule')
                ->join('user_profiles as up2', 'acr.responsable_matricule', '=', 'up2.user_matricule')
                ->join('structures as str', 'str.structure_matricule', '=', 'acr.structure_matricule')
                ->where('acr.agent_matricule', $matricule)
                ->select('up.photo_profil as pp', 'up.prenom as prenom', 'up.nom as nom',
                        'str.nom_structure as nom_structure', 'str.structure_matricule as structure_matricule',
                        'acr.privilege as privilege', 'acr.gestion_stock as stock', 'acr.statut as statut',
                        'acr.role as role', 'up2.prenom as prenom_resp', 'up2.nom as nom_resp', 'acr.created_at as date_ajout')
                ->get();
        }


        return response()->json([
            'success' => true,
            'message'=> "Donnes du produit collectees avec succes",
            'data' => $data,
            'shops' => $shops,
        ]);
    }

    public function makingUpdateForEverything(Request $request)
    {
        $element = $request->input("element");
        $productMatricule = $request->input("matricule");
        $userMatricule = Auth::user()->user_matricule;
        $product = null;
        $structures = null;

        // Récupère le produit
        if ($element === 'productAll' || $element === 'productOn'){
            $product = All_product::where('product_matricule', $productMatricule)->first();
//            $product2 = Online_product::where('product_matricule', $productMatricule)->first();
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => "Produit non trouvé.",
                    'type' => 'updatingEverything',
                ]);
            }
        }

        // Si l’élément est un produit complet
        if ($element === "productAll") {
            // Vérification des autorisations
            $authorized = Structure::where('user_matricule', $userMatricule)
                    ->where('structure_matricule', $product->structure_matricule)
                    ->exists()
                || Accreditation::where('agent_matricule', $userMatricule)
                    ->where('structure_matricule', $product->structure_matricule)
                    ->exists();

            if (!$authorized) {
                return response()->json([
                    'success' => false,
                    'message' => "Vous n'êtes pas autorisé à effectuer cette modification.",
                    'type' => 'updatingEverything',
                ]);
            }

            // Gestion de l'image
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // Données à mettre à jour
            $data = [
                'nom_produit' => $request->input("nom_produit"),
                'code_barre' => $request->input("code_barre"),
                'description' => $request->input("description"),
            ];
            if ($imagePath) {
                $data['image'] = $imagePath;
            }

            // Comparer les anciennes et nouvelles valeurs
            $changes = [];
            foreach ($data as $field => $newValue) {
                if ($product->$field != $newValue) {
                    $changes[] = [
                        'field' => $field,
                        'before' => $product->$field,
                        'after' => $newValue,
                    ];
                    $product->$field = $newValue;
                }
            }

            // Supprimer l’ancienne image si nécessaire
            if ($request->hasFile('image')) {
                if ($product->image && Storage::exists(str_replace('/storage/', 'public/', $product->image))) {
                    Storage::delete(str_replace('/storage/', 'public/', $product->image));
                }
                $product->image = $imagePath;
            }

            if (!empty($changes)) {
                $product->save();

                // Enregistrer dans editings
                foreach ($changes as $change) {
                    Editing::create([
                        'edit_matricule'        => "edit-" . now()->format('YmdHis'),
                        'edited_matricule'      => $product->product_matricule,
                        'table'                 => 'all_products',
                        'field_edited'          => $change['field'],
                        'before_value'          => $change['before'],
                        'after_value'           => $change['after'],
                        'structure_matricule'   => $product->structure_matricule,
                        'responsable_matricule' => $userMatricule,
                    ]);
                }
            }
        }
        elseif ($element === "productOn") {
            $authorized = Structure::where('user_matricule', $userMatricule)
                    ->where('structure_matricule', $product->structure_matricule)
                    ->exists()
                || Accreditation::where('agent_matricule', $userMatricule)
                    ->where('structure_matricule', $product->structure_matricule)
                    ->exists();

            if (!$authorized) {
                return response()->json([
                    'success' => false,
                    'message' => "Vous n'êtes pas autorisé à effectuer cette modification.",
                    'type' => 'updatingEverything',
                ]);
            }

            // Récupération du produit concerné
//            $numeroFacture = $request->input('numero_facture');
//            $productMatricule = $request->input('product_matricule');
            $productsOn = Online_product::where('product_matricule', $productMatricule)
//                ->where('numero_facture', $numeroFacture)
                ->get();

            if ($productsOn->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "Aucun produit correspondant trouvé.",
                ]);
            }

            // Gestion de l'image
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // Données à mettre à jour
            $data = [
                'prix_achat' => $request->input('prix_achat'),
                'nom_produit' => $request->input('nom_produit'),
                'marge' => $request->input('marge'),
                'statut' => $request->input('statut'),
            ];

            if ($imagePath) {
                $data['image'] = $imagePath;
            }

            foreach ($productsOn as $product) {
                $changes = [];

                // Vérifie les différences champ par champ
                foreach ($data as $field => $newValue) {
                    if ($product->$field != $newValue) {
                        $changes[] = [
                            'field' => $field,
                            'before' => $product->$field,
                            'after' => $newValue,
                        ];
                        $product->$field = $newValue;
                        if($field === 'marge'){
                            $product->prix_vente = $data['prix_vente'] * $data['marge'];
                        }
                    }
                }

                // Enregistrer la nouvelle image si modifiée
                if ($request->hasFile('image')) {
                    // Supprimer l’ancienne image si elle existe
                    if ($product->image && Storage::exists(str_replace('/storage/', 'public/', $product->image))) {
                        Storage::delete(str_replace('/storage/', 'public/', $product->image));
                    }
                    $product->image = $imagePath;
                }

                if (!empty($changes)) {
                    $product->save();

                    // Enregistrer les modifications dans editings
                    foreach ($changes as $change) {
                        Editing::create([
                            'edit_matricule'       => "edit-" . now()->format('YmdHis'),
                            'edited_matricule'     => $productMatricule,
                            'table'                => 'online_products',
                            'field_edited'         => $change['field'],
                            'before_value'         => $change['before'],
                            'after_value'          => $change['after'],
                            'structure_matricule'  => $product->structure_matricule,
                            'responsable_matricule'=> $userMatricule,
                        ]);
                    }
                }
            }
        }
        elseif ($element === "bills"){
            $credentials = $request->validate(
                [
                    'numero_facture' => ['required', 'string'],
                    'total_facture' => ['required', 'numeric'],
                    'facture' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:2048'],
                ],
                [
                    'numero_facture.required' => 'Le numéro de la facture est obligatoire.',
                    'total_facture.required' => 'Le total de la facture est requis pour mettre à jour la facture.',
                    'total_facture.numeric' => 'Le total de la facture doit être un nombre valide.',
                ]
            );

            $matricule = $request->input('matricule');
            $numeroFacture = $credentials['numero_facture'];
            $totalFacture = $credentials['total_facture'];

            if (str_contains($matricule, 'charge')) {
                $oldInfos = Charge::where('charge_matricule', $matricule)->first();

                if (!$oldInfos) {
                    return response()->json([
                        'success' => false,
                        'message' => "Aucune charge trouvée avec le matricule fourni.",
                    ]);
                }

                // Vérifie si quelque chose a changé
                $changes = [];

                if ($numeroFacture !== $oldInfos->numero_facture) {
                    $changes[] = [
                        'table' => 'charges',
                        'field' => 'numero_facture',
                        'before' => $oldInfos->numero_facture,
                        'after' => $numeroFacture,
                    ];
                    $oldInfos->numero_facture = $numeroFacture;
                }

                if ($totalFacture != $oldInfos->total_facture) {
                    $changes[] = [
                        'table' => 'charges',
                        'field' => 'total_facture',
                        'before' => $oldInfos->total_facture,
                        'after' => $totalFacture,
                    ];
                    $oldInfos->total_facture = $totalFacture;
                }

                if ($request->hasFile('facture')) {
                    // Supprimer l’ancien fichier s’il existe
                    if ($oldInfos->facture && Storage::exists(str_replace('/storage/', 'public/', $oldInfos->facture))) {
                        Storage::delete(str_replace('/storage/', 'public/', $oldInfos->facture));
                    }

                    // Enregistrer le nouveau fichier
                    $file = $request->file('facture');
                    $fileName = 'facture_' . now()->format('YmdHis'). '.' . $file->getClientOriginalExtension();
                    $newFilePath = $file->storeAs('factures', $fileName, 'public');

                    $changes[] = [
                        'table' => 'charges',
                        'field' => 'facture',
                        'before' => $oldInfos->facture ?? 'Aucun',
                        'after' => $newFilePath,
                    ];

                    $oldInfos->facture = $newFilePath;
                }
                // Enregistrer les modifications
                if (!empty($changes)) {
                    $oldInfos->save();

                    foreach ($changes as $change) {
                        Editing::create([
                            'edit_matricule' => "edit-".now()->format('YmdHis'),
                            'table' => $change['table'],
                            'edited_matricule' => $matricule,
                            'field_edited' => $change['field'],
                            'before_value' => $change['before'],
                            'after_value' => $change['after'],
                            'structure_matricule' => $oldInfos->structure_matricule,
                            'responsable_matricule' => $userMatricule,
                        ]);
                    }
                }

            }
            elseif (str_contains($matricule, 'facture')){
                $oldInfos = Facture::where('facture_matricule', $matricule)->first();
                if (!$oldInfos) {
                    return response()->json([
                        'success' => false,
                        'message' => "Aucune facture trouvée avec le matricule fourni.",
                    ]);
                }
                $changes = [];
                $marge = $request-> input('marge');
                $fournisseur_matricule = $request->input('fournisseur');

                if($oldInfos->marge !== $marge){
                    $changes [] = [
                        'table' => 'factures',
                        'field' => 'marge',
                        'before' => $oldInfos->marge,
                        'after' => $marge
                    ];
                    $oldInfos->marge = $marge;
                }
                if ($oldInfos->fournisseur_matricule !== $fournisseur_matricule){
                    $changes [] = [
                        'table' => 'factures',
                        'field' => 'fournisseur_matricule',
                        'before' => $oldInfos->fournisseur->fournisseur_matricule,
                        'after' => $fournisseur_matricule
                    ];
                    $oldInfos->fournisseur_matricule = $fournisseur_matricule;
                }
                if ($numeroFacture !== $oldInfos->numero_facture) {
                    $changes[] = [
                        'table' => 'factures',
                        'field' => 'numero_facture',
                        'before' => $oldInfos->numero_facture,
                        'after' => $numeroFacture,
                    ];
                    $oldInfos->numero_facture = $numeroFacture;
                }

                if ($totalFacture != $oldInfos->total_facture) {
                    $changes[] = [
                        'table' => 'factures',
                        'field' => 'total_facture',
                        'before' => $oldInfos->total_facture,
                        'after' => $totalFacture,
                    ];
                    $oldInfos->total_facture = $totalFacture;
                }

                if ($request->hasFile('facture')) {
                    // Supprimer l’ancien fichier s’il existe
                    if ($oldInfos->facture && Storage::exists(str_replace('/storage/', 'public/', $oldInfos->facture))) {
                        Storage::delete(str_replace('/storage/', 'public/', $oldInfos->facture));
                    }

                    // Enregistrer le nouveau fichier
                    $file = $request->file('facture');
                    $fileName = 'facture_' . now()->format('YmdHis'). '.' . $file->getClientOriginalExtension();
                    $newFilePath = $file->storeAs('factures', $fileName, 'public');

                    $changes[] = [
                        'table' => 'factures',
                        'field' => 'facture',
                        'before' => $oldInfos->facture ?? 'Aucun',
                        'after' => $newFilePath,
                    ];

                    $oldInfos->facture = $newFilePath;
                }

                // Enregistrer les modifications
                if (!empty($changes)) {
                    $oldInfos->save();

                    foreach ($changes as $change) {
                        Editing::create([
                            'edit_matricule' => "edit-".now()->format('YmdHis'),
                            'edited_matricule' => $matricule,
                            'table' => $change['table'],
                            'field_edited' => $change['field'],
                            'before_value' => $change['before'],
                            'after_value' => $change['after'],
                            'structure_matricule' => $oldInfos->structure_matricule,
                            'responsable_matricule' => $userMatricule,
                        ]);
                        if ($change['table'] === 'factures' && $change['field'] === 'marge'){
                            $onProductsInfos = Online_product::where('facture_matricule', $oldInfos->facture_matricule)->get();
                            foreach ($onProductsInfos as $product){
                                $product->prix_vente = $marge * $product->prix_achat;
                                $product->marge = $marge;
                                $product->save();
                            }
//                            $onProductsInfos->save();
                        }
                    }
                }
            }

        }
        elseif ($element === 'dealers'){
            $matricule = $request->input('matricule');
            $dealerInfos = Fournisseur::where('fournisseur_matricule', $matricule)->first();
            $changes = [];
            $credentials = $request->validate(
                [
                    'nom_fournisseur'       => ['required', 'string', 'max:100'],
                    'sigle_fournisseur'     => ['nullable', 'string', 'max:30'],
                    'adresse'               => ['nullable', 'string', 'max:255'],
                    'email'               => ['nullable', 'string', 'max:255', 'unique:fournisseurs'],
                    'telephone1'              => ['required', 'string', 'max:15', 'unique:fournisseurs'],
                    'telephone2'              => ['nullable', 'string', 'max:15', 'unique:fournisseurs'],
                    'telephone_fixe'          => ['nullable', 'string', 'max:15', 'unique:fournisseurs'],
                    'logo'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                    'description'           => ['required', 'string'],
                    'nom_agent'             => ['nullable', 'string', 'max:100'],
                    'numero_agent'          => ['nullable', 'string', 'max:15', 'unique'],
                ],

                [
                    // nom_fournisseur
                    'nom_fournisseur.required' => 'Le nom du fournisseur est obligatoire.',
                    'nom_fournisseur.string'   => 'Le nom du fournisseur doit être une chaîne de caractères.',
                    'nom_fournisseur.max'      => 'Le nom du fournisseur ne doit pas dépasser 100 caractères.',

                    // sigle_fournisseur
                    'sigle_fournisseur.string' => 'Le sigle du fournisseur doit être une chaîne de caractères.',
                    'sigle_fournisseur.max'    => 'Le sigle du fournisseur ne doit pas dépasser 50 caractères.',

                    // adresse
                    'adresse.string' => 'L’adresse doit être une chaîne de caractères.',
                    'adresse.max'    => 'L’adresse ne doit pas dépasser 255 caractères.',

                    // email
                    'email.string' => 'L’email doit être une chaîne de caractères.',
                    'email.max'    => 'L’email ne doit pas dépasser 255 caractères.',
                    'email.unique'    => 'Cette adresse mail est deja associee a un autre fournisseur',
                    // si tu ajoutes le rule email: 'email.email' => 'Le format de l’email est invalide.',

                    // telephone1
                    'telephone1.required' => 'Le telephone principal est obligatoire.',
                    'telephone1.string'   => 'Le telephone principal doit être une chaîne de caractères.',
                    'telephone1.max'      => 'Le telephone principal ne doit pas dépasser 15 chiffres.',

                    'telephone1.unique'    => 'Ce numero de telephone principal est deja attribue a un autre fournisseur.',

                    // telephone2
                    'telephone2.string' => 'Le telephone secondaire doit être une chaîne de caractères.',
                    'telephone2.max'    => 'Le telephone secondaire ne doit pas dépasser 15 chiffres.',

                    'telephone2.unique'    => 'Ce numero de telephone secondaire est deja attribue a un autre fournisseur.',


                    // telephone_fixe
                    'telephone_fixe.string' => 'Le telephone fixe doit être une chaîne de caractères.',
                    'telephone_fixe.max'    => 'Le telephone fixe ne doit pas dépasser 15 chiffres.',

                    'telephone_fixe.unique'    => 'Ce numero de telephone fixe est deja attribue a un autre fournisseur.',


                    // logo
                    'logo.image'    => 'Le fichier fourni doit être une image.',
                    'logo.mimes'    => 'L’image doit être au format : jpg, jpeg, png ou webp.',
                    'logo.max'      => 'L’image ne doit pas dépasser 2 Mo.',

                    // description
                    'description.required' => 'La description est obligatoire.',
                    'description.string'   => 'La description doit être une chaîne de caractères.',

                    // nom_agent
                    'nom_agent.string' => 'Le nom de l’agent doit être une chaîne de caractères.',
                    'nom_agent.max'    => 'Le nom de l’agent ne doit pas dépasser 100 caractères.',

                    // numero_agent
                    'numero-agent.string' => 'Le numéro de l’agent doit être une chaîne de caractères.',
                    'numero-agent.max'    => 'Le numéro de l’agent ne doit pas dépasser 15 chiffres.',

                ]
            );

            $fieldFournisseur = ['nom_fournisseur', 'sigle_fournisseur', 'adresse', 'email', 'telephone1', 'telephone2', 'telephone_fixe', 'logo', 'description', 'statut', 'nom_agent', 'telephone_agent'];
            foreach ($fieldFournisseur as $field){
                if ($dealerInfos->$field !== $credentials[$field]){
                    $changes [] = [
                      'field' => $field,
                      'table' => 'fournisseurs',
                      'before' => $dealerInfos->$field,
                      'after' =>   $credentials[$field],
                    ];
                }
            }
            if ($request->hasFile('logo')) {
                // Supprimer l’ancien logo s’il existe
                if ($dealerInfos->logo && Storage::exists(str_replace('/storage/', 'public/', $dealerInfos->logo))) {
                    Storage::delete(str_replace('/storage/', 'public/', $dealerInfos->logo));
                }

                // Enregistrer le nouveau logo
                $file = $request->file('logo');
                $fileName = 'logo_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                $newFilePath = $file->storeAs('fournisseurs', $fileName, 'public');

                $changes[] = [
                    'table' => 'fournisseurs',
                    'field' => 'logo',
                    'before' => $dealerInfos->logo ?? 'Aucun',
                    'after' => $newFilePath,
                ];

                $dealerInfos->logo = $newFilePath;
            }

            // 🔹 Enregistrer les changements s’il y en a
            if (!empty($changes)) {
                $dealerInfos->save();

                foreach ($changes as $change) {
                    Editing::create([
                        'edit_matricule'        => "edit-" . now()->format('YmdHis'),
                        'edited_matricule'      => $matricule,
                        'table'                 => $change['table'],
                        'field_edited'          => $change['field'],
                        'before_value'          => $change['before'],
                        'after_value'           => $change['after'],
                        'structure_matricule'   => $dealerInfos->structure_matricule,
                        'responsable_matricule' => $userMatricule,
                    ]);
                }
            }
        }
        elseif ($element === "employes"){
            $changes = [];
            $privilege = $request->input('privilege');
            $gestion_stock = $request->input('gestion_stock');
            $role = $request->input('role');
            $statut = $request->input('statut');
            $liste = ['privilege' => $privilege, 'gestion_stock' => $gestion_stock, 'role' => $role, 'statut' => $statut];
            $data_teste = [];

            $editMatricule = 'edit-'.now()->format("YmdHis");
            $fonctionMatricule = User::where('user_matricule', $productMatricule)->first();
            $privilegeInfos = Accreditation::where('agent_matricule', $productMatricule)->get();
            $structures = collect();
            $structures = $structures -> merge(Structure::where('user_matricule', $userMatricule)->get());
            $structures = $structures->merge(Accreditation::where('agent_matricule', $userMatricule)
                                                ->where('privilege', '=', 'eleve')
                                                ->get());


            foreach ($liste as $item => $value){
                foreach ($privilegeInfos as $privilege){
                    if ($privilege->$item !== $value){
                        $privilege->$item = $value;
                        $privilege->save();
                        if($privilege->$item === "role"){
                            $fonctionMatricule->fonction = $privilege->$value;
                            $fonctionMatricule->save();
                        }
                        $changes[]=[
                            'field' => $item,
                            'before' => $privilege->$item,
                            'after' => $value,
                            'table' => 'accreditations',
                            'structure_matricule' => $privilege->structure_matricule,
                        ];
                    }
                }
            }
            foreach ($structures as $component){
                if ($request->input($component->structure_matricule) === 'on'){
                    $etat = Accreditation::where('structure_matricule', $component->structure_matricule)
                                            ->where('agent_matricule', $productMatricule)
                                            ->first();
                    if ($etat){
                        if ($etat->statut === 'inactif'){
                            $etat->statut = 'actif';
                            $etat->save();
                            $changes[]=[
                                'field' => 'statut',
                                'before' => 'inactif',
                                'after' => 'actif',
                                'table' => 'accreditations',
                                'structure_matricule' => $etat->structure_matricule,
                            ];
                        }
                    }else{
                        Accreditation::create([
                            'accreditation_matricule' => 'accreditation-'.now()->format("YmdHis"),
                            'agent_matricule' => $productMatricule,
                            'structure_matricule' => $component->structure_matricule,
                            'responsable_matricule' => $userMatricule,
                            'role' => $role,
                            'privilege' => $privilege,
                            'statut' => $statut,
                            'gestion_stock' => $gestion_stock,
                        ]);


                    }
                }else{
                    $etat = Accreditation::where('structure_matricule', $component->structure_matricule)
                        ->where('agent_matricule', $productMatricule)
                        ->first();
                    if ($etat) {
                        if ($etat->statut === 'actif') {
                            $etat->statut = 'inactif';
                            $etat->save();

                            $changes[]=[
                                'field' => 'statut',
                                'before' => 'actif',
                                'after' => 'inactif',
                                'table' => 'accreditations',
                                'structure_matricule' => $etat->structure_matricule,
                            ];
                        }
                    }
                }
            }

            foreach ($changes as $change) {
                Editing::create([
                    'edit_matricule'        => "edit-" . now()->format('YmdHis'),
                    'edited_matricule'      => $userMatricule,
                    'table'                 => $change['table'],
                    'field_edited'          => $change['field'],
                    'before_value'          => $change['before'],
                    'after_value'           => $change['after'],
                    'structure_matricule'   => $change['structure_matricule'],
                    'responsable_matricule' => $userMatricule,
                ]);
            }

        }

        return response()->json([
            'success' => true,
            'message' => 'Mise à jour effectuée avec succès.',
            'type' => 'updatingEverything',
            'data' => $data_teste,
        ]);
    }

    public function addNewIngredient(Request $request)
    {
        $user_matricule = Auth::user()->user_matricule;
        $credentials = $request->validate(
            [
                'description' => ['required', 'string'],
                'ingredient' => ['required', 'string'],
                'unite' => ['required', 'string'],
                'categorie' => ['required', 'string'],
                'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'description.required' => 'La description est requise.',
                'ingredient.required' => 'Le nom de l’ingrédient est requis.',
                'unite.required' => "L’unité de mesure est requise.",
                'image.image' => "Le fichier doit être une image.",
                'image.mimes' => "L’image doit être au format jpg, jpeg, png ou webp.",
                'image.max' => "L’image ne doit pas dépasser 2 Mo.",
                'image.required' => "L’image de l’ingrédient est requise.",
                'categorie.required' => "Veuillez renseigner une categorie.",
            ]
        );

        // Vérifie et sauvegarde l'image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('products', $filename, 'public');
        } else {
            return response()->json([
                'success' => false,
                'message' => "L'image est obligatoire",
            ]);
        }

        // Création de l’ingrédient
        Ingredient::create([
            'ingredient_matricule' => 'ingredient-'.now()->format("YmdHis"),
            'description' => $credentials['description'],
            'nom' => $credentials['ingredient'],
            'unite_mesure' => $credentials['unite'],
            'responsable_matricule' => $user_matricule,
            'image' => $imagePath,
        ]);

        // Réponse JSON
        return response()->json([
            'success' => true,
            'message' => "Nouvel ingrédient sauvegardé avec succès",
        ]);
    }

    public function gettingAccountDetails(Request $request){

        $userMatricule = Auth::user()->user_matricule;
        $data = User::where('user_matricule', $userMatricule)->first(['email', 'telephone1']);

        return response()->json([
            'success' => true,
            'message' => "Donnees du compte",
            'data' => $data,
        ]);
    }

    public function updateDetailsAccount(Request $request){
        $credentials = $request->validate(
            [
                'email' => ['required', 'email'],
                'telephone1' => ['required', 'string', 'max:15', 'min:7'],
                'password' => [ 'required', 'string'],
            ],
            [
                'email.required' => "Une adresse mail est requise.",
                'email.email' => 'Veuillez entrer une adresse mail valide.',
                'telephone1.required' => 'Un numéro de téléphone principal est requis.',
                'telephone1.max' => 'Le numéro de téléphone ne doit pas dépasser 15 chiffres.',
                'telephone1.min' => 'Le numéro de téléphone doit comporter au moins 7 chiffres.',
                'password.required' => 'Votre mot de passe est requis pour valider les mises à jour.',
            ]
        );

        $user = Auth::user(); // ✅ get the currently logged-in user

        // 🔐 Check if the password entered matches the one stored in DB
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'type' => 'checkingPasswordUpdatingAccountDetails',
                'message' => 'Le mot de passe est incorrect.',
            ]);
        }

        $oldInfosEmail = User::where('email', $request->email)
                                    ->where('user_matricule', '!=', $user->user_matricule)
                                    ->first();
        $oldInfosTelephone1 = User::where('telephone1', $request->telephone1)
                                        ->where('user_matricule', '!=', $user->user_matricule)
                                        ->first();
        $oldInfosTelephone2 = User_profile::where('telephone2', $request->telephone1)
                                            ->where('user_matricule', '!=', $user->user_matricule)
                                            ->first();
        if (!$oldInfosEmail){
            if(!$oldInfosTelephone1){
                if(!$oldInfosTelephone2){
                    $user->update([
                        'email' => $request->email,
                        'telephone1' => $request->telephone1,
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'type' => 'checkingPasswordUpdatingAccountDetails',
                        'message' => 'Ce numero de telephone est deja asocie a un autre compte ',
                    ]);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'type' => 'checkingPasswordUpdatingAccountDetails',
                    'message' => 'Ce numero de telephone est deja asocie a un autre compte ',
                ]);
            }
        }else{
            return response()->json([
                'success' => false,
                'type' => 'checkingPasswordUpdatingAccountDetails',
                'message' => 'Cette adresse mail est deja asociee a un autre compte ',
            ]);
        }

        return response()->json([
            'success' => true,
            'type' => 'checkingPasswordUpdatingAccountDetails',
            'message' => 'Mis a jour effectuee avec succes !',
        ]);
    }

//    protected function updateProductAll(RequestAllProductUpdate $request, All_product $product, array $data)
//    {
//        $validated = $request->validated();
//        $product->update(array_merge($validated, $data));
//    }

//    protected function updateProductOn(RequestProductOnUpdate $request, Online_product $product2, array $data){
//        $validated = $request->validated();
//        $product2->update(array_merge($validated, $data));
//    }


    protected function checkExists($value, $field, $table)
    {
        $user_matricule = auth()->user()->user_matricule;

        return \DB::table($table)
            ->where($field, $value)
            ->where('user_matricule', '!=', $user_matricule)
            ->whereNotNull($field)         // ignore les valeurs NULL
            ->where($field, '!=', '')      // ignore les chaînes vides
            ->exists();
    }

}
