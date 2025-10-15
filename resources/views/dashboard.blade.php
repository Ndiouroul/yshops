<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-store-get-everything" content="{{ route('dashboard-get-everything', ['matricule' => '__SHOPMATRICULE__', 'thing' => '__THING__']) }}">
    <meta name="route-store-get-element-for-update" content="{{ route('getting-Element-For-Updating', ['element' => '__ELEMENT__', 'matricule' => '__MATRICULE__']) }}">
    <meta name="route-store-new-product" content="{{ route('dashboard_new_product') }}">
    <meta name="route-store-get-account-details" content="{{ route('dashboard-get-account-details') }}">
    <meta name="route-store-new-ingredient" content="{{ route('dashboard_new_ingredient') }}">
    <meta name="route-store-new-structure" content="{{ route('dashboard_new_structure') }}">
    <meta name="route-store-new-fournisseur" content="{{ route('dashboard_new_fournisseur') }}">
    <meta name="route-store-update-account-details" content="{{ route('dashboard_update_accout_details') }}">
    <meta name="route-store-element-updating" content="{{ route('dashboard-element-updating') }}">
    <meta name="route-store-new-manager" content="{{ route('dashboard_new_manager') }}">
    <meta name="route-store-new-seller" content="{{ route('dashboard_new_seller') }}">
    <meta name="route-store-get-profil" content="{{ route('get-profil') }}">
    <meta name="route-store-get-all-shops-stock" content="{{ route('get-all-shops-stock') }}">
    <meta name="route-store-get-shop" content="{{ route('get-shop') }}">
    <meta name="route-store-get-bill-products" content="{{ route('get-bill-products', ['shopname' => '__SHOPNAME__']) }}">
    <meta name="route-store-get-shop-sold-data" content="{{ route('get-shop-sold-data', ['shopmatricule' => '__SHOPMATRICULE__']) }}">
    <meta name="route-store-get-shop-data-sold-fees" content="{{ route('get-shop-sold-data-fees', ['shopmatricule' => '__SHOPMATRICULE__']) }}">
    <meta name="route-store-get-dealer-list" content="{{ route('get-dealers', ['shopname' => '__SHOPNAME__']) }}">
    <meta name="route-store-get-all-shops-products" content="{{ route('get-all-shops-products', ['shopname' => '__SHOPNAME__']) }}">
    <meta name="route-store-add-new-bill-products" content="{{ route('add-new-bill')}}">
    <meta name="route-store-add-new-bill-transformation-products" content="{{ route('add-new-bill-transformation')}}">
    <meta name="route-store-get-stock" content="{{ route('get-stock') }}">
    <meta name="route-store-edit-profil" content="{{ route('edit-profil') }}">
    <meta name="route-store-check-password" content="{{ route('check-password') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css','resources/js/dashboard-script.js'])
    <title>Dashboard</title>
</head>
<body class="bg-gray-200 w-screen h-screen">
<div class="bg-white shadow-md px-5 py-1 flex justify-between items-center fixed w-full top-0 z-100">
    <p class="text-2xl font-bold text-blue-600">Y-Shops</p>
    <div class="flex space-x-6 items-center">
        <a href="{{route('accueil')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Accueil</a>
        <a href="{{route('dashboard')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px] font-bold">Dashboard</a>
        @if(Auth::check())
            @gestionStock
            <a href="{{route('shop')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Shop</a>
            @endgestionStock
            @vente
            <a href="{{route('vente')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Vente</a>
            @endvente
            @can('manage-users')
                <a href="{{route('admin')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Admin</a>
            @endcan

                <p class="text-drkgray-700 hover:text-blue-600 text-[13px] font-bold text-blue-600">{{ Auth::user()->pseudo }} </p>
            <img src="{{ asset('storage/' . session('photo_profil')) }}" alt="Photo de profil" class="w-8 h-8 rounded-[50%] text-[8px]">
            <a href="{{route('deconnexion')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Deconnexion</a>

        @else
            <a href="{{route('login')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Connexion</a>
            <a href="{{route('inscription')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Inscription</a>
        @endif
    </div>
</div>
<div class="flex w-full h-full gap-3 pt-[50px] pr-[10px] pl-[10px] pb-[10px]">
    <!-- Sidebar -->
    <div id="gauche" class="grid h-auto w-[170px] min-w-[170px] bg-white shadow-md rounded-md pt-5 overflow-auto">
        <ul class="flex flex-col gap-3 w-full h-full p-3 text-[13px]">
            @manager
                <li id="stats" class="h-[35px] flex justify-right items-center cursor-pointer hover:bg-blue-200 pl-[5px] rounded-md hover:border hover:border-blue-600">Statistique</li>
                <li id="comptabilite" class="h-[35px] flex justify-right items-center cursor-pointer hover:bg-blue-200 pl-[5px] rounded-md hover:border hover:border-blue-600">Comptabilite</li>
            @endmanager
            @gestionStock
                <li id="vente" class="h-[35px] flex justify-right items-center cursor-pointer hover:bg-blue-200 pl-[5px] rounded-md hover:border hover:border-blue-600">Vente</li>
                <li id="boutique" class="h-[35px] flex justify-right items-center cursor-pointer hover:bg-blue-200 pl-[5px] rounded-md hover:border hover:border-blue-600">Boutique</li>
            @endgestionStock
            <li id="parametre" class="h-[35px] flex justify-right items-center cursor-pointer hover:bg-blue-200 pl-[5px] rounded-md hover:border hover:border-blue-600">Parametre</li>
        </ul>
    </div>

    <!-- Contenu principal -->
    <div id="droite" class="grid h-auto w-full shadow-md rounded-md p-1 relative overflow-auto">
        <div id="bloc-stats" class="hidden flex flex-wrap gap-2 justify-center">
{{--            <canvas id="myChart" class="w-[400px] h-[200px] bg-white shadow-md"></canvas>--}}
        </div>
        <div id="bloc-comptabilite" class="hidden flex flex-wrap gap-2 justify-center"></div>
        <div id="bloc-vente" class="hidden">Vente bloc</div>
        <div id="bloc-boutique" class="hidden">
            <div id="entete-boutton-boutique" class="flex justify-end w-full h-[30px] gap-2">
                <button id="button_add_new_shop" class="rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Nouvelle boutique</button>
            </div>
            <div id="bloc-add-new-shop" class="absolute top-0 w-full left-0  h-full z-55 bg-[rgba(0,0,0,0.7)] grid place-items-center transform -translate-x-[150%] transition-transform duration-300 overflow-auto px-3 py-15">
                <form id="form-add-new-shop" class="relative bg-white px-3 py-10 rounded-lg shadow-lg w-[75%]">
                    <span id="close-add-new-shops" class="absolute top-[10px] right-[10px] text-[12px] cursor-pointer px-3 py-[2px] rounded-md bg-red-500 text-white">Fermer</span>
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                    <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Creation nouvelle boutique</h2>

                    <!-- Message global -->
                    <div id="global-message-new-shop" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <div class="w-full h-full flex flex-wrap gap-4 pl-3">

                        <!-- Nom complet -->
                        <div class="relative w-[47%] mb-4">
                            <input id="nom_structure" type="text" name="nom_structure" placeholder=" "
                                   value="{{ old('nom_structure') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="nom_structure-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="nom_structure"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Nom complet Exp: Grpupe Yessal-Gui
                            </label>

                        </div>

                        <!-- Sigle -->
                        <div class="relative w-[47%] mb-4">
                            <input id="sigle_structure" type="text" name="sigle_structure" placeholder=" "
                                   value="{{ old('sigle_structure') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="sigle_structure-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="sigle_structure"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Sigle Exp: GY
                            </label>
                        </div>

                        <!-- Téléphone principal -->
                        <div class="relative w-[47%] mb-4">
                            <input id="telephone1" type="text" name="telephone1" placeholder=" "
                                   value="{{ old('telephone1') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="telephone1-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="telephone1"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Téléphone principal
                            </label>
                        </div>

                        <!-- Téléphone secondaire -->
                        <div class="relative w-[47%] mb-4">
                            <input id="telephone2" type="text" name="telephone2" placeholder=" "
                                   value="{{ old('telephone2') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="telephone2-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="telephone2"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Téléphone secondaire
                            </label>
                        </div>

                        <!-- Téléphone fixe -->
                        <div class="relative w-[47%] mb-4">
                            <input id="telephone-fixe" type="text" name="telephone-fixe" placeholder=" "
                                   value="{{ old('telephone-fixe') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="telephone-fixe-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="telephone-fixe"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Téléphone fixe
                            </label>
                        </div>

                        <!-- Email de la structure -->
                        <div class="relative w-[47%] mb-4">
                            <input id="email_structure" type="email" name="email_structure" placeholder=" "
                                   value="{{ old('email_structure') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="email_structure-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="email_structure"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Email de la structure
                            </label>
                        </div>

                        <!-- Adresse -->
                        <div class="relative w-[47%] mb-4">
                            <input id="adresse_structure" type="text" name="adresse_structure" placeholder=" "
                                   value="{{ old('adresse_structure') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="adresse_structure-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="adresse_structure"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Adresse de la structure
                            </label>
                        </div>

                        <div class="relative w-[47%] mb-4">
                            <select id="type_structure" name="type_structure"
                                    class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]">
                                <option value="">Type</option>
                                <option value="transformation">Transformation</option>
                                <option value="revente">Revente</option>
                            </select>
                            <span id="type_structure-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="type_structure"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Tyde de structure
                            </label>
                        </div>

                        <div id="bloc-catehorie_structure" class="relative w-[47%] mb-4 hidden">
                            <select id="cathegorie_structure" name="cathegorie_structure"
                                    class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]">
                                <option value="">Catégorie</option>
                                <option value="alimentaire">Alimentaire</option>
                                <option value="usinage">Usinage</option>
                                <option value="other">Autre</option>
                            </select>
                            <span id="cathegorie_structure-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="cathegorie_structure"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Catégorie
                            </label>
                        </div>

                        <!-- Logo -->
                        <div class="relative w-[47%] mb-4">
                            <input id="logo" type="file" name="logo" placeholder=" "
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                            <span id="logo-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="logo"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Logo
                            </label>
                        </div>

                        <!-- Description -->
                        <div class="relative w-[47%] mb-4">
                    <textarea id="description" name="description" rows="3"  cols="10" placeholder=" "
                              class="peer w-full px-3 border border-gray-300 rounded-md
                         focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px] pt-2">{{ old('description') }}</textarea>
                            <span id="description-error" class="error-message text-red-600 text-[12px]"></span>
                            <label for="description"
                                   class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
              peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
              peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
              peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
              bg-white px-1">
                                Description
                            </label>
                        </div>
                    </div>

                    <button type="submit"
                            class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Creer
                    </button>
                </form>
            </div>
        </div>
        <div id="bloc-boutique-container" class="flex flex-col overflow-auto items-center gap-5 hidden">
            <div id="entete-boutton" class="flex justify-center self-center w-auto h-auto gap-2 rounded-md bg-white items-center py-2 px-2">
                <button id="button_view_produits" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Produits</button>
                <button id="button_view_fournisseurs" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Fournisseurs</button>
                <button id="button_view_factures" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Factures</button>
                <button id="button_view_vendeurs" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Vendeurs</button>
                <button id="button_view_gerants" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Gerants</button>
                <hr class="w-[90%] text-black h-1">
            </div>
            <div id="bloc-view-produits" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto">
                <button id="button_add_new_produit" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Ajouter produit</button>
                <h4>Produits de base</h4>
                <table id="table-All" class="w-full h-auto border border-collapse border-gray-400 bg-white shadow-md rounded-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-400 px-4 py-2">Nom</th>
                            <th class="border border-gray-400 px-4 py-2">Code-barre</th>
                            <th class="border border-gray-400 px-4 py-2">Date d'ajout</th>
                            <th class="border border-gray-400 px-4 py-2">Responsable</th>
                            <th class="border border-gray-400 px-4 py-2">Details</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <h4>Produits en ligne</h4>
                <table id="table-On" class="w-full h-auto border border-collapse border-gray-400 bg-white shadow-md rounded-md">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-400 px-4 py-2">Nom</th>
                        <th class="border border-gray-400 px-4 py-2">Prix d'achat</th>
                        <th class="border border-gray-400 px-4 py-2">Prix de vente</th>
                        <th class="border border-gray-400 px-4 py-2">Date</th>
                        <th class="border border-gray-400 px-4 py-2">Responsable</th>
                        <th class="border border-gray-400 px-4 py-2">Details</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>
            </div>
            <div id="bloc-view-fournisseurs" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto">
                <button id="button_add_new_fournisseur" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Ajouter founisseur</button>
                <table class="w-full h-auto border border-collapse border-gray-400 bg-white shadow-md rounded-md">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-400 px-4 py-2">Prenom</th>
                        <th class="border border-gray-400 px-4 py-2">Nom</th>
                        <th class="border border-gray-400 px-4 py-2">Contact</th>
                        <th class="border border-gray-400 px-4 py-2">Statut</th>
                        <th class="border border-gray-400 px-4 py-2">Details</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div id="bloc-view-factures" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto">
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>

            </div>
            <div id="bloc-view-vendeurs" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto">
                <button id="button_add_new_vendeur" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Nouveau vendeur</button>
                <table class="w-full h-auto border border-collapse border-gray-400 bg-white shadow-md rounded-md">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-400 px-4 py-2">Prenom</th>
                        <th class="border border-gray-400 px-4 py-2">Nom</th>
                        <th class="border border-gray-400 px-4 py-2">Contact</th>
                        <th class="border border-gray-400 px-4 py-2">Statut</th>
                        <th class="border border-gray-400 px-4 py-2">Privilège</th>
                        <th class="border border-gray-400 px-4 py-2">Details</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>

            </div>
            <div id="bloc-view-gerants" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto">
                <button id="button_add_new_gerant" class="hover:bg-blue-800 transition duration-300 w-[85%] rounded-md bg-blue-600 px-5 py-2 flex text-center items-center text-[13px] text-white cursor-pointer">Nouveau gerant</button>
                <table class="w-full h-auto border border-collapse border-gray-400 bg-white shadow-md rounded-md">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-400 px-4 py-2">Prenom</th>
                        <th class="border border-gray-400 px-4 py-2">Nom</th>
                        <th class="border border-gray-400 px-4 py-2">Contact</th>
                        <th class="border border-gray-400 px-4 py-2">Statut</th>
                        <th class="border border-gray-400 px-4 py-2">Privilège</th>
                        <th class="border border-gray-400 px-4 py-2">Details</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>

            </div>
            <div id="bloc-add-new-produit" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto">
                <form id="form-add-new-produit" class="text-[12px] bg-white px-8 py-6 rounded-lg shadow-lg max-w-md w-full">
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                    <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Ajout nouveau produit</h2>

                    <!-- Message global -->
                    <div id="global-message-new-produit" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <!-- Champ Pseudo -->
                    <div class="mb-4 text-[12px]">
                        <label for="nom_produit" class="block mb-2 font-semibold text-gray-600">Nom du produit</label>
                        <input id="nom_produit" type="text" name="nom_produit" value="{{ old('nom_produit') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="nom_produit-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                    </div>

                    <!-- Champ Nom -->
                    <div class="mb-4">
                        <label for="code_barre" class="block mb-2 font-semibold text-gray-600">Code barre</label>
                        <input id="code_barre" type="text" name="code_barre" value="{{ old('code_barre') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                        <span id="code_barre-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                    </div>

                    <div id="add-new-product-price" class="mb-4 hidden">
                        <label for="prix" class="block mb-2 font-semibold text-gray-600">Prix</label>
                        <input id="prix" type="text" name="prix" value="{{ old('prix')?0:0 }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                        <span id="prix-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 text-[12px]">
                        <label for="description" class="block mb-2 font-semibold text-gray-600">Description</label>
                        <textarea id="description" cols="50" rows="5" name="description" value="{{ old('description') }}"
                                  class="text-[12px] w-full px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none"></textarea>
                        <span id="description-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="block mb-2 font-semibold text-gray-600">Image du produit</label>
                        <input id="image" type="file" name="image" value="{{ old('image') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="image-error" class="text-red-600 text-[11px] mt-1 block error-message"></span>
                    </div>

                    <!-- Bouton -->
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-green-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Creer
                    </button>
                </form>
            </div>
            <div id="bloc-add-new-fournisseur" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto px-3 py-15">
                <form id="form-add-new-fournisseur" class="bg-white px-8 py-6 rounded-lg shadow-lg max-w-md w-full">
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                    <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Nouveau fournisseur</h2>

                    <!-- Message global -->
                    <div id="global-message-new-fournisseur" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <!-- Champ Pseudo -->
                    <div class="mb-4 text-[13px]">
                        <label for="nom_fournisseur" class="block mb-2 font-semibold text-gray-600">Nom du fournisseur</label>
                        <input id="nom_fournisseur" type="text" name="nom_fournisseur" value="{{ old('nom_fournisseur') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="nom_fournisseur-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="sigle_fournisseur" class="block mb-2 font-semibold text-gray-600">Sigle</label>
                        <input id="sigle_fournisseur" type="text" name="sigle_fournisseur" value="{{ old('sigle_fournisseur') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="sigle_fournisseur-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="telephone1" class="block mb-2 font-semibold text-gray-600">Telephone</label>
                        <input id="telephone1" type="text" name="telephone1" value="{{ old('telephone1') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="telephone1-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="telephone2" class="block mb-2 font-semibold text-gray-600">Telephone secondaire</label>
                        <input id="telephone2" type="text" name="telephone2" value="{{ old('telephone2') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="telephone2-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="telephone_fixe" class="block mb-2 font-semibold text-gray-600">Telephone Fixe</label>
                        <input id="telephone_fixe" type="text" name="telephone_fixe" value="{{ old('telephone_fixe') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="telephone_fixe-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="nom_agent" class="block mb-2 font-semibold text-gray-600">Nom agent de la structure</label>
                        <input id="nom_agent" type="text" name="nom_agent" value="{{ old('nom_agent') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="nom_agent-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="numero_agent" class="block mb-2 font-semibold text-gray-600">Telephone agent de la structure</label>
                        <input id="numero_agent" type="text" name="numero_agent" value="{{ old('numero_agent') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="numero_agent-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <!-- Champ Email -->
                    <div class="mb-4 text-[13px]">
                        <label for="email" class="block mb-2 font-semibold text-gray-600">Email de la structure</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="email-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="adresse" class="block mb-2 font-semibold text-gray-600">Adresse de la structure</label>
                        <input id="adresse" type="text" name="adresse" value="{{ old('adresse') }}"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="adresse-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="description" class="block mb-2 font-semibold text-gray-600">Description</label>
                        <textarea id="description" cols="50" rows="5" name="description" value="{{ old('description') }}"
                                  class="text-[12px] w-full px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none"></textarea>
                        <span id="description-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <div class="mb-4 text-[13px]">
                        <label for="logo" class="block mb-2 font-semibold text-gray-600">Logo</label>
                        <input id="logo" type="file" name="logo"
                               class="text-[12px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="logo-error" class="text-red-600 text-sm mt-1 block text-[10px] error-message"></span>
                    </div>

                    <!-- Bouton -->
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-green-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Creer
                    </button>
                </form>
            </div>
            <div id="bloc-add-new-vendeur" class="w-full h-full relative basis-[100%] hidden left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto px-3 py-15">
                <form id="form-add-new-vendeur" class="bg-white px-8 py-6 rounded-lg shadow-lg min-w-[500px] w-full">
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                    <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Nouveau vendeur</h2>

                    <!-- Message global -->
                    <div id="global-message-new-vendeur" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                   <div class="w-full h-full flex flex-wrap gap-5">
                       <!-- Champ Pseudo -->
                       <div class="relative w-[47%] mb-4">
                           <input id="pseudo" type="text" name="pseudo" placeholder=" "
                                  value="{{ old('pseudo') }}"
                                  class="peer w-full min-w-[200px] h-7 px-3 border border-gray-300 rounded-md
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                           <span id="pseudo-error" class="error-message text-red-600 text-[12px]"></span>
                           <label for="pseudo"
                                  class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                  peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
                  peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
                  peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
                  bg-white px-1">
                               pseudo <span class="text-red-700 font-bold">*</span>
                           </label>

                       </div>


                       <div class="relative w-[47%] mb-4">
                           <input id="telephone1" type="text" name="telephone1" placeholder=" "
                                  value="{{ old('telephone1') }}"
                                  class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                           <span id="telephone1-error" class="error-message text-red-600 text-[12px]"></span>
                           <label for="telephone1"
                                  class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                  peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
                  peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
                  peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
                  bg-white px-1">
                               Telephone principal <span class="text-red-700 font-bold">*</span>
                           </label>

                       </div>


                       <div class="relative w-[47%] mb-4">
                           <input id="email" type="text" name="email" placeholder=" "
                                  value="{{ old('email') }}"
                                  class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                           <span id="email-error" class="error-message text-red-600 text-[12px]"></span>
                           <label for="email"
                                  class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                  peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
                  peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
                  peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
                  bg-white px-1">
                               Adresse mail <span class="text-red-700 font-bold">*</span>
                           </label>

                       </div>

                       <!-- Champ Password -->
                       <div class="relative w-[47%] mb-4">
                           <input id="password" type="text" name="password" placeholder=" "
                                  value="{{ old('password') }}"
                                  class="peer w-full h-7 px-3 border border-gray-300 rounded-md
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                           <span id="password-error" class="error-message text-red-600 text-[12px]"></span>
                           <label for="password"
                                  class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                  peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
                  peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
                  peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
                  bg-white px-1">
                               Mot de passe <span class="text-red-700 font-bold">*</span>
                           </label>

                       </div>

                       <!-- Champ Confirm Password -->
                       <div class="relative w-[47%] mb-4">
                           <input id="password_confirmation" type="text" name="password_confirmation" placeholder=" "
                                  value="{{ old('password_confirmation') }}"
                                  class="peer w-full min-w-[200px] h-7 px-3 border border-gray-300 rounded-md
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[12px]"/>
                           <span id="password_confirmation-error" class="error-message text-red-600 text-[12px]"></span>
                           <label for="password_confirmation"
                                  class="absolute left-2 top-1 text-gray-400 text-[13px] transition-all
                  peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
                  peer-focus:-top-3 peer-focus:text-[13px] peer-focus:text-blue-600
                  peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-[13px] peer-not-placeholder-shown:text-blue-600
                  bg-white px-1">
                               Confirmaton Mot de passe <span class="text-red-700">*</span>
                           </label>

                       </div>
                   </div>
                    <!-- Bouton -->
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-800 cursor-pointer text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Ajouter
                    </button>
                </form>
            </div>
            <div id="bloc-add-new-gerant" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto px-3 py-15">
                <form id="form-add-new-gerant" class="bg-white px-8 py-6 rounded-lg shadow-lg max-w-md w-full">
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                    <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Nouveau gerant</h2>

                    <!-- Message global -->
                    <div id="global-message-new-gerant" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <!-- Champ Pseudo -->
                    <div class="mb-4">
                        <label for="pseudo" class="block mb-2 font-semibold text-gray-600">Pseudo</label>
                        <input id="pseudo" type="text" name="pseudo" value="{{ old('pseudo') }}"
                               class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="pseudo-error" class="text-red-600 text-sm mt-1 block"></span>
                    </div>

                    <div class="mb-4">
                        <label for="telephone1" class="block mb-2 font-semibold text-gray-600">Telephone</label>
                        <input id="telephone1" type="text" name="telephone1" value="{{ old('telephone1') }}"
                               class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="telephone1-error" class="text-red-600 text-sm mt-1 block"></span>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block mb-2 font-semibold text-gray-600">Adresse mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="email-error" class="text-red-600 text-sm mt-1 block"></span>
                    </div>

                    <!-- Champ Password -->
                    <div class="mb-4">
                        <label for="password" class="block mb-2 font-semibold text-gray-600">Mot de passe</label>
                        <input id="password" type="password" name="password"
                               class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="password-error" class="text-red-600 text-sm mt-1 block"></span>
                    </div>

                    <!-- Champ Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block mb-2 font-semibold text-gray-600">Confirmer le mot de passe</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <span id="password_confirmation-error" class="text-red-600 text-sm mt-1 block"></span>
                    </div>
                    <!-- Bouton -->
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-900 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Ajouter
                    </button>
                </form>
            </div>
            <div id="bloc-add-new-ingredient" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto px-3 py-15">
                <form id="form-add-new-ingredient" class="bg-white px-8 py-6 rounded-lg shadow-lg max-w-md w-full">
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                    <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Nouveau ingredient</h2>

                    <!-- Message global -->
                    <div id="global-message-new-ingredient" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <!-- Champ Pseudo -->
                    <div class="mb-4 text-[14px]">
                        <label for="ingredient" class="block mb-2 font-semibold text-gray-600">Nom de l'ingredient</label>
                        <input id="ingredient" type="text" name="ingredient" value="{{ old('ingredient') }}"
                               class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="ingredient-error" class="text-red-600 text-[12px] mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 text-[14px]">
                        <label for="unite" class="block mb-2 font-semibold text-gray-600">Unite de mesure</label>
                        <select id="unite"  name="unite"
                                class="text-[13px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                            <option value="">Unite de mesure</option>
                            <option value="kilo">Kilogramme</option>
                            <option value="litre">Litre</option>
                            <option value="m^2">Mettre carre</option>
                            <option value="m^3">Mettre cube</option>
                            <option value="other">Autre</option>
                        </select>
                        <span id="unite-error" class="text-red-600 text-[12px] mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 text-[14px]">
                        <label for="description" class="block mb-2 font-semibold text-gray-600">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="text-[13px] text-gray-700 p-2 w-full overflow-auto border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none"></textarea>
                        <span id="description-error" class="text-red-600 text-[12px] mt-1 block error-message"></span>
                    </div>

                    <div class="mb-6">
                        <label for="image" class="text-[13px] block mb-2 font-semibold text-gray-600">Image de l'ingredient</label>
                        <input id="image" type="file" name="image"
                               class="text-[13px] w-full h-7 px-3 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="image-error" class="text-red-600 text-[12px] mt-1 block error-message"></span>
                    </div>
                    <!-- Bouton -->
                    <button type="submit"
                            class="relative w-full bg-blue-600 hover:bg-blue-700 cursor-pointer text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        <span id="loader" class="left-[-150px] hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                            <span class="w-8 h-8 border-4 border-white-500 border-dashed rounded-full animate-spin left-[50px]"></span>
                        </span>
                        Ajouter
                    </button>
                </form>
            </div>
            <div id="bloc-add-new-facture" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto">
            </div>
            <div id="bloc-view-update-produits-all" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transition-transform duration-300 overflow-auto relative">
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>

            </div>
            <div id="bloc-view-update-produits-on" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transform -translate-x-[150%] transition-transform duration-300 overflow-auto">
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>
            </div>
            <div id="bloc-view-update-fournisseurs" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transform -translate-x-[150%] transition-transform duration-300 overflow-auto">
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>
            </div>
            <div id="bloc-view-update-employes" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transform -translate-x-[150%] transition-transform duration-300 overflow-auto">
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>
            </div>
            <div id="bloc-view-update-factures" class="w-full h-full relative basis-[100%] hidden h-full left-0 w-full h-[calc(100%-4rem)] z-50 bg-transparent grid place-items-center transform -translate-x-[150%] transition-transform duration-300 overflow-auto">
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                </div>
            </div>
            <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
            </div>
        </div>
        <div id="bloc-parametre" class="hidden flex flex-wrap gap-2">
            <div id="profil-param" class="flex flex-col text-center  w-[200px] h-[150px] shadow-md rounded-md bg-white cursor-pointer justify-center" >
                <p class="text-1xl">Profil</p>
                <hr class="p-7 dorder border-blue-500 w-8/10 items-center">
                <p class="text-[13px]">Mettre a jour</p>
            </div>
{{--            @managerall--}}
                <div id="package-param" class="flex flex-col text-center  w-[200px] h-[150px] shadow-md rounded-md bg-white cursor-pointer justify-center" >
                    <p class="text-1xl">Packages</p>
                    <hr class="p-7 dorder border-blue-500 w-8/10 items-center">
                    <p class="text-[13px]">Consulter / Souscrir</p>
                </div>
{{--            @endmanagerall--}}
            <div id="theme-param" class="flex flex-col text-center w-[200px] h-[150px] shadow-md rounded-md bg-white cursor-pointer justify-center" >
                <p class="text-1xl">Theme</p>
                <hr class="p-7 dorder border-blue-500 w-8/10 items-center">
                <p class="text-[13px]">Sombre / Clair</p>
            </div>
            <div id="compte-param" class="flex flex-col text-center w-[200px] h-[150px] shadow-md rounded-md bg-white cursor-pointer justify-center" >
                <p class="text-1xl">Compte</p>
                <hr class="p-7 dorder border-blue-500 w-8/10 items-center">
                <p class="text-[13px]">Gerrer</p>
            </div>
{{--            @managerall--}}
                <div id="boutique-param" class="flex flex-col text-center w-[200px] h-[150px] shadow-md rounded-md bg-white cursor-pointer justify-center" >
                    <p class="text-1xl">Boutiques</p>
                    <hr class="p-7 dorder border-blue-500 w-8/10 items-center">
                    <p class="text-[13px]">Creer / Editer / Lister</p>
                </div>
{{--            @endmanagerall--}}
            <div id="bloc-update-profil" class="hidden bg-gray-200 absolute h-full w-full top-0 py-5 z-50 grid place-items-center transform transition-transform duration-500 overflow-auto px-3 py-15">
                <form id="form-update-profil" class="bg-white px-8 py-6 rounded-lg shadow-lg w-[60%]">
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->
                    <div id="entete_update_profil" class="flex justify-between">
                        <!-- Titre -->
                        <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Profil</h2>
                        <button type="button" id="annuler-update-profil"
                                class="w-auto h-[30px] px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-[14px] font-semibold transition duration-300 cursor-pointer">
                            Fermer
                        </button>
                    </div>
                    <!-- Message global -->
                    <div id="global-message-new-update-profil" class="hidden text-green-700 bg-green-50 font-bold mb-4 text-center p-1 rounded border border-green-300">
                    </div>

                    <!-- Champ Pseudo -->
                    <div class="mb-4 flex">
                        <label for="nom" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%] w-[40%]">Nom de famille</label>
                        <input id="nom" type="text" name="nom" value="{{ old('nom') }}" disabled
                               class="bg-gray-100 bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="nom-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 flex">
                        <label for="prenom" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Prenom</label>
                        <input id="prenom" type="text" name="prenom" value="{{ old('prenom') }}" disabled
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="prenom-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 flex">
                        <label for="telephone1" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Telephone</label>
                        <input id="telephone1" type="text" name="telephone1" value="{{ old('telephone1') }}" disabled
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="telephone1-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 flex">
                        <label for="telephone2" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Telephone secondaire</label>
                        <input id="telephone2" type="text" name="telephone2" value="{{ old('telephone2') }}" disabled
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="telephone2-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    {{--                        <div class="mb-4 flex">--}}
                    {{--                            <label for="telephone-fixe" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Telephone Fixe</label>--}}
                    {{--                            <input id="telephone-fixe" type="text" name="telephone-fixe" value="{{ old('telephone-fixe') }}" disabled--}}
                    {{--                                   class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">--}}
                    {{--                            <span id="telephone-fixe-error" class="text-red-600 text-sm mt-1 block error-message"></span>--}}
                    {{--                        </div>--}}

                    <div class="mb-4 flex">
                        <label for="profil" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Profil</label>
                        <select id="profil" name="profil" value="{{ old('profil') }}" disabled
                                class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13px]">
                            <option value="">Profil</option>
{{--                            <option value="livreur">Livreur</option>--}}
                            <option value="vendeur">Vendeur</option>
                            <option value="simple_user">Simple utilisateur</option>
                            <option value="mixte">Mixte</option>
                        </select>
                        <span id="profil-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 flex">
                        <label for="sexe" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Sexe</label>
                        <select id="sexe" name="sexe" value="{{ old('sexe') }}" disabled
                                class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13px]">
                            <option value="sexe">Sexe</option>
                            <option value="H">Homme</option>
                            <option value="F">Femme</option>
                        </select>
                        <span id="sexe-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <!-- Champ Email -->
                    <div class="mb-4 flex">
                        <label for="email" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" disabled
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="email-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 flex">
                        <label for="adresse" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Adresse</label>
                        <input id="adresse" type="text" name="adresse" value="{{ old('adresse') }}" disabled
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="adresse-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 flex">
                        <label for="date_naissance" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Date de naissance</label>
                        <input id="date_naissance" type="date" name="date_naissance" value="{{ old('date_naissance') }}" disabled
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13px]">
                        <span id="date_naissance-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <div class="mb-4 flex">
                        <label for="photo_profil-output" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Photo de profil</label>
                        <div class="flex w-full justify-evenly">
                            <div id="photo_profil-output"
                                 class="bg-gray-100 w-30 h-30 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                                <img src="#" alt="Photo de profil" class="text-[13px] w-full h-full rounded-md p-[5px]">
                            </div>
                            <span id="photo_profil-error" class="text-red-600 text-sm mt-1 block error-message"></span>

                            <div id="icon-actualiser" class="grid w-10 h-10 flex items-center justify-center bg-gray-200 rounded-full cursor-pointer hover:bg-gray-300" title="Actualiser la photo de profil">
                                <i class="fas fa-sync"></i>
                            </div>
                            <span id="pnoto-name" class="text-[13px]"></span>
                        </div>
                    </div>

                    <div class="mb-4 flex hidden">
                        <label for="photo_profil" class=" block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Photo de profil</label>
                        <input id="photo_profil" name="photo_profil" type="file"
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                    </div>

                    <div class="mb-4 flex hidden">
                        <label for="cni" class=" block mb-2 font-semibold text-gray-600 text-[14px] w-[60%]">Carte nationnal d'identite</label>
                        <input id="cni" type="text" name="cni"
                               class="bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <span id="cni-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>

                    <!-- Bouton -->
                    <button type="button" id="button-update-profil-button"
                            class="w-full bg-blue-600 hover:bg-blue-700 cursor-pointer text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Mettre a jour
                    </button>
                    <button type="submit" id="button-update-profil-submit"
                            class="hidden w-full bg-blue-600 hover:bg-blue-700 cursor-pointer text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Valider
                    </button>
                </form>
            </div>
            <div id="bloc-check-password" class="absolute top-0 h-full w-full left-0 z-55 bg-[rgba(0,0,0,0.7)] grid place-items-center transform -translate-x-[150%] transition-transform duration-500 overflow-auto py-0">
                <form id="form-check-password" class="bg-white px-8 py-4 rounded-lg shadow-lg w-[40%]">
                @csrf <!-- Protection CSRF obligatoire dans Laravel -->
                    <div id="entete-check-password" class="flex justify-between">
                        <!-- Titre -->
                        <h2 class="text-center text-[14px] font-bold text-gray-700 mb-6">Verification du mot de passe</h2>

                        <button type="button" id="annuler-check-password"
                                class="flex items-center text-center w-auto h-[20px] px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-[13px] font-semibold transition duration-300 cursor-pointer">
                            Annuler
                        </button>
                    </div>
                    <!-- Message global -->
                    <div id="global-message-check-password" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <!-- Champ Pseudo -->
                    <div class="mb-4 grid">
                        <div class="flex">
                            <label for="password" class="block mb-2 font-semibold text-gray-600 text-[14px] w-[60%] w-[40%]">Mot de passe</label>
                            <input id="password" type="password" name="password" value="{{ old('password') }}"
                                   class="bg-gray-100 bg-gray-100 w-full h-7 px-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 outline-none text-[13p]">
                        </div>
                        <span id="password-error" class="text-red-600 text-sm mt-1 block error-message"></span>
                    </div>
                    <button type="submit" id="button-check-password"
                            class="w-full bg-blue-600 hover:bg-blue-700 cursor-pointer text-white py-1 rounded-md text-lg font-semibold transition duration-300">
                        Valider
                    </button>
                </form>
            </div>
            <div id="bloc-option-param-boutique" class="flex hidden bg-gray-200 absolute w-[100%] h-full top-0 z-50 transform -translate-x-[150%] transition-transform duration-500 overflow-auto px-[5px] py-5">
                <div class="flex flex-col bg-white shadow-md w-[200px] min-w-[200px] items-center justify-center">
                    <p id="button-creer-new-shop" class="text-white rounded-md text-center mt-2 bg-blue-600 px-[7px] py-[3px] text-[13px] cursor-pointer hover:bg-blue-700">Creer nouvelle boutique</p>
                    <p id="button-view-shops" class="text-white rounded-md text-center mt-2 bg-blue-600 px-[7px] py-[3px] text-[13px] cursor-pointer hover:bg-blue-700">Boutique existant</p>
                </div>
                <div class="max-w-[calc(100%-200px)] h-full flex">
                    <div id="bloc-add-new-shop-param" class=" bg-gray-200 absolute h-full w-auto top-0 py-5 z-50 grid place-items-center transform transition-transform duration-500 overflow-auto px-3 py-15">
                        <form id="form-add-new-shop-param" class="relative bg-white px-3 py-10 rounded-lg shadow-lg w-[75%]">
                            <span id="close-add-new-shops-param" class="absolute top-[10px] right-[10px] text-[12px] cursor-pointer px-3 py-[2px] rounded-md bg-red-500 text-white">Annuler</span>
                        @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                            <!-- Titre -->
                            <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Creation nouvelle boutique</h2>

                            <!-- Message global -->
                            <div id="global-message-new-shop-param" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                            </div>

                            <div class="w-full h-full flex flex-wrap gap-4 pl-3">

                                <!-- Nom complet -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="nom_structure" type="text" name="nom_structure" placeholder=" "
                                           value="{{ old('nom_structure') }}"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>

                                    <label for="nom_structure"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black
           bg-white px-1 z-0">
                                        Nom complet
                                    </label>
                                    <span id="nom_structure-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Sigle -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="sigle_structure" type="text" name="sigle_structure" placeholder=" "
                                           value="{{ old('sigle_structure') }}"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>

                                    <label for="sigle_structure"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-placeholder-shown:top-1
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black
           bg-white px-1 z-0">
                                        Sigle (Ex: YS)
                                    </label>
                                    <span id="sigle_structure-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Téléphone principal -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="telephone1" type="text" name="telephone1" placeholder=" "
                                           value="{{ old('telephone1') }}"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>

                                    <label for="telephone1"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-placeholder-shown:top-1
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black
           bg-white px-1 z-0">
                                        Téléphone principal
                                    </label>
                                    <span id="telephone1-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Téléphone secondaire -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="telephone2" type="text" name="telephone2" placeholder=" "
                                           value="{{ old('telephone2') }}"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>

                                    <label for="telephone2"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-placeholder-shown:top-1
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black
           bg-white px-1 z-0">
                                        Téléphone secondaire
                                    </label>
                                    <span id="telephone2-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Téléphone fixe -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="telephone-fixe" type="text" name="telephone-fixe" placeholder=" "
                                           value="{{ old('telephone-fixe') }}"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>

                                    <label for="telephone-fixe"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-placeholder-shown:top-1
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black
           bg-white px-1 z-0">
                                        Téléphone fixe
                                    </label>
                                    <span id="telephone-fixe-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Email de la structure -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="email_structure" type="email" name="email_structure" placeholder=" "
                                           value="{{ old('email_structure') }}"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>

                                    <label for="email_structure"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-placeholder-shown:top-1
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black
           bg-white px-1 z-0">
                                        Email de la structure
                                    </label>
                                    <span id="email_structure-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Adresse -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="adresse_structure" type="text" name="adresse_structure" placeholder=" "
                                           value="{{ old('adresse_structure') }}"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>

                                    <label for="adresse_structure"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-placeholder-shown:top-1
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black
           bg-white px-1 z-0">
                                        Adresse de la structure
                                    </label>
                                    <span id="adresse_structure-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Type de structure -->
                                <div class="relative w-[47%] mb-4">
                                    <select id="type_structure" name="type_structure"
                                            class="peer w-full h-7 px-3 border border-gray-300 rounded-md
            focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
            bg-transparent text-gray-900 z-10 relative">
                                        <option value="">Type</option>
                                        <option value="transformation">Transformation</option>
                                        <option value="revente">Revente</option>
                                    </select>
                                    <label for="type_structure"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           bg-white px-1 z-0">
                                    </label>
                                    <span id="type_structure-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Catégorie -->
                                <div id="bloc-cathegorie_structure-param" class="relative w-[47%] mb-4 hidden">
                                    <select id="cathegorie_structure" name="cathegorie_structure"
                                            class="peer w-full h-7 px-3 border border-gray-300 rounded-md
            focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
            bg-transparent text-gray-900 z-10 relative">
                                        <option value="">Catégorie</option>
                                        <option value="alimentaire">Alimentaire</option>
                                        <option value="usinage">Usinage</option>
                                        <option value="other">Autre</option>
                                    </select>
                                    <label for="cathegorie_structure"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           bg-white px-1 z-0">
                                    </label>
                                    <span id="cathegorie_structure-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Logo -->
                                <div class="relative w-[47%] mb-4">
                                    <input id="logo" type="file" name="logo"
                                           class="peer w-full h-7 px-3 border border-gray-300 rounded-md
           focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
           bg-transparent text-gray-900 z-10 relative"/>
                                    <label for="logo"
                                           class="absolute left-3 -top-4 text-gray-900 text-[13px] transition-all duration-200
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           bg-white px-1 z-0">
                                        Logo
                                    </label>
                                    <span id="logo-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                                <!-- Description -->
                                <div class="relative w-[47%] mb-4">
    <textarea id="description" name="description" rows="3" placeholder=" "
              class="peer w-full px-3 border border-gray-300 rounded-md
              focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-[13px]
              bg-transparent text-gray-900 pt-2 z-10 relative">{{ old('description') }}</textarea>

                                    <label for="description"
                                           class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
           peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
           bg-white px-1 z-0">
                                        Description
                                    </label>
                                    <span id="description-error" class="error-message text-red-600 text-[12px]"></span>
                                </div>

                            </div>

                            <button type="submit"
                                    class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                                Creer
                            </button>
                        </form>
                    </div>
                    <div id="bloc-shops-param" class="hidden bg-gray-200 absolute h-full w-auto top-0 py-5 z-50 grid place-items-center transform transition-transform duration-500 overflow-auto px-3 py-15">
                        <p>Liste des boutiques existants</p>
                    </div>
                </div>
            </div>
            <div id="bloc-option-param-compte" class="flex hidden bg-gray-200 absolute w-[95%] h-full top-0 z-50 transform -translate-x-[150%] transition-transform duration-500 overflow-auto px-[5px] py-5 items-center justify-center">
                <form id="form-account-update-param" class="flex flex-col items-center justify-center relative bg-white px-3 py-10 rounded-lg shadow-lg w-[75%]">
                    <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                        <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
                    </div>
                    <span id="close-bloc-compte-param" class="absolute top-[10px] right-[10px] text-[12px] cursor-pointer px-3 py-[2px] rounded-md bg-red-500 text-white">Fermer</span>
                    @csrf <!-- Protection CSRF obligatoire dans Laravel -->

                    <!-- Titre -->
                    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Details de votre compte</h2>

                    <!-- Message global -->
                    <div id="global-message-compte-param" class=" w-full hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
                    </div>

                    <div class="mt-4 mb-4 w-full h-full flex flex-col gap-4 justify-center items-center">

                        <!-- Email -->
                        <div class="relative w-[75%] mb-4">
                            <input id="email" type="text" name="email" placeholder=" "
                                   value="{{ old('email') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
         focus:border-gray-500 focus:ring-2 focus:ring-gray-200 outline-none text-[13px]
         bg-transparent text-gray-900 z-10 relative"/>

                            <label for="email"
                                   class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
         peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
         peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
         peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black peer-not-placeholder-shown:text-[13px]
         bg-white px-1 z-0">
                                Email
                            </label>
                            <span id="email-error" class="error-message text-red-600 text-[12px]"></span>
                        </div>

                        <!-- Téléphone principal -->
                        <div class="relative w-[75%] mb-4">
                            <input id="telephone1" type="text" name="telephone1" placeholder=" "
                                   value="{{ old('telephone1') }}"
                                   class="peer w-full h-7 px-3 border border-gray-300 rounded-md
         focus:border-gray-500 focus:ring-2 focus:ring-gray-200 outline-none text-[13px]
         bg-transparent text-gray-900 z-10 relative"/>

                            <label for="telephone1"
                                   class="absolute left-3 top-2 text-gray-400 text-[13px] transition-all duration-200
         peer-placeholder-shown:top-1 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
         peer-focus:-top-5 peer-focus:text-[14px] peer-focus:font-bold peer-focus:text-black
         peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black peer-not-placeholder-shown:text-[13px]
         bg-white px-1 z-0">
                                Téléphone principal
                            </label>
                            <span id="telephone1-error" class="error-message text-red-600 text-[12px]"></span>
                        </div>

                        <!-- Mot de passe actuel -->
                        <div class="relative w-[75%] m2-4 p-3 bg-blue-500 rounded-md">
                            <input id="password" type="password" name="password" placeholder="Mot de passe actuel"
                                   value="{{ old('password') }}"
                                   class="peer bg-white w-full h-7 px-3 border border-gray-300 rounded-md border-none outline-none
         focus:border-gray-500 focus:ring-2 focus:ring-gray-200 outline-none text-[13px]
         bg-transparent text-gray-900 z-10 relative"/>

                            <label for="password"
                                   class="absolute left-3 top-2 text-white text-[13px] transition-all duration-200 bg-blue-500
         peer-placeholder-shown:top-1 peer-placeholder-shown:hidden peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-[13px]
         peer-focus:-top-5 peer-focus:text-[14px] peer-focus:flex peer-focus:font-bold peer-focus:text-black
         peer-not-placeholder-shown:-top-5 peer-not-placeholder-shown:font-bold peer-not-placeholder-shown:text-black peer-not-placeholder-shown:text-[13px]
         bg-white px-1 z-0">
                                Votre mot de passe actuel (Pour valider les mise a jours)
                            </label>
                        </div>
                        <span id="password-error" class="error-message text-red-600 text-[12px]"></span>


                    </div>

                    <button type="submit"
                            class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                        Creer
                    </button>
                </form>
            </div>
            <div id="bloc-option-param-packages" class="grid gap-3 hidden bg-gray-200 absolute w-[98%] h-full top-0 z-50 transform -translate-x-[150%] transition-transform duration-500 overflow-auto px-[5px] py-5">
                <span id="close-option-packages-param" class="relative left-[80%] w-auto h-[30px] px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-[14px] font-semibold transition duration-300 cursor-pointer">
                    Fermer
                </span>
                <div class="flex flex-wrap justify-center gap-7">
                    <!-- Classic -->
                    <div class="w-[230px] h-auto rounded-md shadow-md bg-white p-3 relative">
                        <h3 class="font-bold text-[15px] py-2 bg-blue-600 text-white text-center rounded-md shadow-md mb-4">Classic (Gratuit)</h3>
                        <p class="text-[10px] font-bold mb-2 text-blue-400">
                            Idéal pour commencer gratuitement.
                        </p>
                        <span class="text-[13px]">-> 1 boutique</span><br>
                        <span class="text-[13px]">-> 2 produits (2 images chacun)</span><br>
                        <span class="text-[13px]">-> 100 ventes</span><br>
                        <span class="text-[13px]">-> 25 abonnements</span><br>
                        <button id="Classic (Gratuit)" type="button" class="w-[90%] text-[14px] absolute bottom-[7px] bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-bold px-3 py-1 mt-2 rounded-md shadow-md">
                            Souscrire
                        </button>
                    </div>

                    <!-- Super -->
                    <div class="w-[230px] h-auto rounded-md shadow-md bg-white p-3 relative">
                        <h3 class="font-bold text-[15px] py-2 bg-blue-600 text-white text-center rounded-md shadow-md mb-4">Super (5 000 FCFA)</h3>
                        <p class="text-[10px] font-bold mb-2 text-blue-400">
                            Pour les petites structures qui veulent monter en gamme.
                        </p>
                        <span class="text-[13px]">-> 2 boutiques</span><br>
                        <span class="text-[13px]">-> 10 produits (5 images chacun)</span><br>
                        <span class="text-[13px]">-> Ventes illimitées</span><br>
                        <span class="text-[13px]">-> 100 abonnements</span><br>
                        <button id="Super (5 000 FCFA)" type="button" class="w-[90%] text-[14px] absolute bottom-[7px] bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-bold px-3 py-1 mt-2 rounded-md shadow-md">
                            Souscrire
                        </button>
                    </div>

                    <!-- Extra -->
                    <div class="w-[230px] h-auto rounded-md shadow-md bg-white p-3 relative">
                        <h3 class="font-bold text-[15px] py-2 bg-blue-600 text-white text-center rounded-md shadow-md mb-4">Extra (10 000 FCFA)</h3>
                        <p class="text-[10px] font-bold mb-2 text-blue-400">
                            Pour les commerçants en pleine croissance.
                        </p>
                        <span class="text-[13px]">-> 4 boutiques</span><br>
                        <span class="text-[13px]">-> 50 produits (7 images chacun)</span><br>
                        <span class="text-[13px]">-> Ventes illimitées</span><br>
                        <span class="text-[13px]">-> 1000 abonnements</span><br>
                        <button id="Extra (10 000 FCFA)" type="button" class="w-[90%] text-[14px] absolute bottom-[7px] bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-bold px-3 py-1 mt-2 rounded-md shadow-md">
                            Souscrire
                        </button>
                    </div>

                    <!-- Diamond -->
                    <div class="w-[230px] h-auto rounded-md shadow-md bg-white p-3 relative">
                        <h3 class="font-bold text-[15px] py-2 bg-blue-600 text-white text-center rounded-md shadow-md mb-4">Diamond (25 000 FCFA)</h3>
                        <p class="text-[10px] font-bold mb-2 text-blue-400">
                            Passez à l’échelle et ne vous limitez plus.
                        </p>
                        <span class="text-[13px]">-> 10 boutiques</span><br>
                        <span class="text-[13px]">-> 250 produits (10 images chacun)</span><br>
                        <span class="text-[13px]">-> Ventes illimitées</span><br>
                        <span class="text-[13px]">-> Abonnements illimités</span><br>
                        <button id="Diamond (25 000 FCFA)" type="button" class="w-[90%] text-[14px] absolute bottom-[7px] bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-bold px-3 py-1 mt-2 rounded-md shadow-md">
                            Souscrire
                        </button>
                    </div>

                    <!-- Elite -->
                    <div class="w-[230px] h-auto rounded-md shadow-md bg-white p-3 relative">
                        <h3 class="font-bold text-[15px] py-2 bg-blue-600 text-white text-center rounded-md shadow-md mb-4">Elite (50 000 FCFA)</h3>
                        <p class="text-[10px] font-bold mb-2 text-blue-400">
                            Pour les grandes structures nécessitant flexibilité et puissance.
                        </p>
                        <span class="text-[13px]">-> 15 boutiques</span><br>
                        <span class="text-[13px]">-> Produits illimités</span><br>
                        <span class="text-[13px]">-> Ventes illimitées</span><br>
                        <span class="text-[13px]">-> Abonnements illimités</span><br>
                        <button id="Elite (50 000 FCFA)" type="button" class="w-[90%] text-[14px] absolute bottom-[7px] bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-bold px-3 py-1 mt-2 rounded-md shadow-md">
                            Souscrire
                        </button>
                    </div>

                    <!-- Super Elite -->
                    <div class="w-[230px] h-auto rounded-md shadow-md bg-white p-3 relative">
                        <h3 class="font-bold text-[15px] py-2 bg-blue-600 text-white text-center rounded-md shadow-md mb-4">Super Elite (100 000 FCFA)</h3>
                        <p class="text-[10px] font-bold mb-2 text-blue-400">
                            L’offre ultime pour gérer un grand réseau de boutiques.
                        </p>
                        <span class="text-[13px]">-> 100 boutiques</span><br>
                        <span class="text-[13px]">-> Produits illimités</span><br>
                        <span class="text-[13px]">-> Ventes illimitées</span><br>
                        <span class="text-[13px]">-> Abonnements illimités</span><br>
                        <button type="button" class="w-[90%] text-[14px] absolute bottom-[7px] bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-bold px-3 py-1 mt-2 rounded-md shadow-md">
                            Souscrire
                        </button>
                    </div>
                    <div>
                        <form action="#" id="form-valide-souscription-package">
                            <select name="pakage" id="package">
                                <option value="package">Packages</option>
                            </select>
                            <select name="shop" id="shop">
                                <option value="">Boutiques</option>
                            </select>
                            <input type="text" id="validation-code-transfert" name="validation-code-transfert" placeholder="ID de transaction. Exp: ">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
