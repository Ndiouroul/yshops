<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-get-shop-details" content="{{ route('shopGetDetails') }}">
    <meta name="route-send-commande-details" content="{{ route('shopSendCommande') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css','resources/js/shop-script.js'])
    <title>Shop</title>
</head>
<body class="bg-gray-200 w-screen h-screen">
    <div class="bg-white shadow-md px-5 py-1 flex justify-between items-center fixed w-full top-0 z-100">
        <p class="text-2xl font-bold text-blue-600">Y-Shops</p>
        <div class="flex space-x-6 items-center">
            <a href="{{route('accueil')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Accueil</a>
            <a href="{{route('dashboard')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Dashboard</a>

            @if(Auth::check())
                <a href="{{route('shop')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px] font-bold">Shop</a>
                <a href="{{route('vente')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Vente</a>
                <a href="{{route('deconnexion')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Deconnexion</a>
                <a href="{{route('admin')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Admin</a>
                <p class="text-drkgray-700 hover:text-blue-600 text-[13px] font-bold text-blue-600">{{ Auth::user()->pseudo }} </p>
                <img src="{{ asset('storage/' . session('photo_profil')) }}" alt="Photo de profil" class="w-8 h-8 rounded-[50%]">
            @endif
            <p id="blocCommandePara" class="w-auto h-auto cursor-pointer"><i class="fa fa-shopping-cart"></i><span id="panierCommandeEtat" class="absolute top-[2px] w-3 h-3 hidden text-red-600 text-bold rounded-[50%] text-center text-[12px]">1</span></p>
            @if(!Auth::check())
            <a href="{{route('login')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Connexion</a>
                <a href="{{route('inscription')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Inscription</a>
            @endif
        </div>
    </div>
    <div id="container" class="w-full h-full flex gap-2 pt-12 pb-2 pl-2 pr-2">
        <div id="gauche" class="w-[175px] min-w-[175px] h-full bg-white rounded-md shadow-md">
            <p class="text-[13px]">Rechercher</p>
            <p class="text-[13px]">Contacter Vendeur</p>
            <p class="text-[13px]">S'abonner</p>
            <p class="text-[13px]">Aimer</p>
            <p class="text-[13px]">Commenter</p>
            <p class="text-[13px]">Partager</p>
        </div>
        <div id="droite" class="relative w-[100%] h-[100%] rounded-md overflow-auto flex flex-col gap-4">
            <div id="bloc-details-shop" class="w-full h-50 p-2 flex justify-between bg-white shadow-md items-center">

            </div>
            <div id="bloc-products" class="flex flex-wrap gap-2 w-full h-auto gap-2">

            </div>
            <div id="bloc-show-details-products" class="overflow-auto hidden absolute h-auto min-h-full py-5 w-full bg-[rgba(0,0,0,0.7)] grid place-items-center transform transition-transform duration-500 -translate-x-[150%]">

            </div>
            <div id="bloc-show-details-commande" class="overflow-auto hidden absolute h-auto min-h-full py-5 w-full bg-[rgba(0,0,0,0.7)] grid place-items-center transform transition-transform duration-500 -translate-x-[150%]">
                <span id="fermerPanier" class="absolute top-[60px] right-[40px] bg-red-600 text-white text-[13px] rounded-md px-2 cursor-pointer">fermer</span>
                <form action="#" id="form-send-valide-commande" class="w-[60%] h-auto bg-white rounded-md px-2 py-7">
                    <span id="annulerCommande" class="relative top-[-20px] px-4 py-[2px] bg-orange-600 text-white text-[13px] rounded-md px-2 cursor-pointer">Annuler la commande</span>
                    <fieldset id="espace-ajout-details-commande" class="w-[100%] h-auto flex flex-col gap-1">

                    </fieldset>
                    <fieldset id="espace-ajout-total-general-commande" class="w-full h-auto flex items-center justify-center p-3">

                    </fieldset>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 cursor-pointer text-white font-bold w-full py-4 rounded-md">Valider</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
