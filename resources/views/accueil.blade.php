<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-get-shop-details" content="{{ route('shopGetDetails') }}">
    <meta name="route-get-shop-details-accueil" content="{{ route('accueil-get-shops') }}">
    <meta name="route-open-shop-accueil" content="{{ route('accueil-open-shops', ['matricule' => '__MATRICULE__']) }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css','resources/js/accueil-script.js'])
    <title>Accueil</title>
</head>
<body class="w-screen h-screen">
    <div class="bg-white shadow-md px-5 py-1 flex justify-between items-center fixed w-full top-0 z-100">
        <p class="text-2xl font-bold text-blue-600">Y-Shops</p>
        <div class="flex space-x-6 items-center">
            <a href="{{route('accueil')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px] font-bold">Accueil</a>
            <a href="{{route('dashboard')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Dashboard</a>

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
    <div id="container" class="pt-12 flex w-full h-full bg-gray-100 p-2 gap-2">
        <div id="bloc-gauche" class=" w-[200px] max-w-[200px] h-full bg-white rounded-md">
            <form id="recherche-avancee-accueil" action="#" class="w-full h-full text-[13px] p-2 flex flex-col gap-3">
                <fieldset class="flex w-full gap-3 justify-between">
                    <select name="element" id="element" class="w-full h-[25px] bg-gray-100 rounded-md border-none outline-none focus:shadow-md">
                        <option value="">Objet</option>
                        <option value="boutique">Boutique</option>
                        <option value="produit">Produit</option>
                    </select>
                </fieldset>
                <input type="text" id="nom-recherche" name="nom-recherche" class="rounded-md bg-gray-100 p-1 outline-none border-none focus:shadow-md transition duration-300" placeholder="Recherche ...">
                <button id="button-submit-search" type="submit" class="w-full h-[30px] rounded-md bg-blue-600 hover:bg-blue-700 transition duration-300 cursor-pointer text-white font-bold">Rechercher</button>

            </form>
        </div>
        <div id="bloc-droite" class="flex flex-col w-full h-full bg-gray-100 rounded-md overflow-auto">
            <div id="entete" class="w-full h-[350px] max-h-[400px] flex gap-3 p-3">
                <div id="gros-titre-description" class="min-w-[350px] h-full flex flex-col items-center justify-center gap-5 bg-white rounded-md shadow-md p-3">
                    <h1 class="text-[25px] font-bold">Gérez vos boutiques, vos produits et vos ventes — simplement et intelligemment.</h1>
                    <h3 class="text-[15px]">Yshops vous aide à centraliser la gestion de vos activités commerciales, du stock à la rentabilité.</h3>
                    <button class="relative top-[25px] left-[20%] bg-blue-600 cursor-pointer hover:bg-blue-700 py-2 px-5 rounded-md shadow-blue-500 text-white text-[13px] font-bold transition duration-300">Commencer gratuitemnt</button>
                </div>
                <div id="bloc-tasks" class="flex flex-wrap gap-5 justify-around h-full overflow-auto ">
                    <div id="bloc1" class="rounded-md p-1 shadow-md cursor-pointer flex flex-col bg-white gap-2 w-[120px] h-[140px] p-2"><h2 class="text-[14px] font-bold">Gestion des Boutiques</h2> <span class="text-[12px] text-gray-900">Créez et gérez plusieurs boutiques depuis un seul espace.</span></div>
                    <div id="bloc2" class="rounded-md p-1 shadow-md cursor-pointer flex flex-col bg-white gap-2 w-[120px] h-[140px] p-2"><h2 class="text-[14px] font-bold">Gestion des produits</h2> <span class="text-[12px] text-gray-900">Ajoutez, modifiez et suivez vos stocks en temps réel.</span></div>
                    <div id="bloc3" class="rounded-md p-1 shadow-md cursor-pointer flex flex-col bg-white gap-2 w-[120px] h-[140px] p-2"><h2 class="text-[14px] font-bold">Ventes et Statistiques</h2> <span class="text-[12px] text-gray-900">Visualisez vos ventes et vos profits par jour, mois ou boutique.</span></div>
                    <div id="bloc4" class="rounded-md p-1 shadow-md cursor-pointer flex flex-col bg-white gap-2 w-[120px] h-[140px] p-2"><h2 class="text-[14px] font-bold">Fournisseurs</h2> <span class="text-[12px] text-gray-900">Centralisez vos fournisseurs et suivez leurs livraisons.</span></div>
                    <div id="bloc5" class="rounded-md p-1 shadow-md cursor-pointer flex flex-col bg-white gap-2 w-[120px] h-[140px] p-2"><h2 class="text-[14px] font-bold">Utilisateurs & Permissions</h2> <span class="text-[12px] text-gray-900">Gérez vos vendeurs, gestionnaires et leurs rôles.</span></div>
                    <div id="bloc6" class="rounded-md p-1 shadow-md cursor-pointer flex flex-col bg-white gap-2 w-[120px] h-[140px] p-2"><h2 class="text-[14px] font-bold">Paramètres & Paiements</h2> <span class="text-[12px] text-gray-900">Configurez votre compte, vos paiements et vos préférences.</span></div>
                </div>
            </div>
            <div id="shops-container" class="w-full h-full">

            </div>
        </div>
    </div>
</body>
</html>
