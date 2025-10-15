<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-valid-Commande" content="{{ route('route-valid-Commande') }}">
    <meta name="route-get-structure" content="{{ route('venteGetStructure') }}">
    <meta name="route-search-Product-Commande" content="{{ route('search-Product-Commande') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/vente-script.js'])
    <title>Vente</title>
</head>
<body class="bg-gray-200 w-screen h-screen">
    <div class="bg-white shadow-md px-5 py-1 flex justify-between items-center fixed w-full h-auto top-0 z-100">
        <p class="text-2xl font-bold text-blue-600">Y-Shops</p>
        <div class="flex space-x-6 items-center">
            <a href="{{route('accueil')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Accueil</a>
            <a href="{{route('dashboard')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Dashboard</a>
            <a href="{{route('admin')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Admin</a>

            @if(Auth::check())
                <a href="{{route('vente')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]  font-bold">Vente</a>
                <a href="{{route('deconnexion')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Deconnexion</a>
                <a href="{{route('shop')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Shop</a>
                <p class="text-drkgray-700 hover:text-blue-600 text-[13px] font-bold text-blue-600">{{ Auth::user()->pseudo }} </p>
            @else
                <a href="{{route('login')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Connexion</a>
                <a href="{{route('inscription')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Inscription</a>
            @endif
        </div>
    </div>
    <div class="w-full h-full pt-[45px] flex flex-col gap-[5px]">
        <div class="w-full h-[40px] shadow-md bg-white text-[13px] flex justify-between items-center text-center pl-[7px] pr-[7px]">
            <div class="flex gap-[30px]">
                <button class="px-2 py-[3px] rounded bg-blue-700 cursor-pointer text-white">Historique</button>
            </div>
            <div class="w-full flex justify-end">
                <form action="#" id="form-vente-recherche" class="flex justify-end w-full">
                    <select name="shopMatricule" id="shopMatricule">

                    </select>
                    <input type="text" id="identifiant" name="identifiant" placeholder="Rechercher par code barre ou par nom"
                           class="rounded-md bg-gray-100 pl-[5px] w-[100%] h-[30px] mr-[5px] max-w-[400px]">
                    <button class="px-2 py-[3px] rounded bg-blue-700 cursor-pointer text-white" type="submit">Valider</button>
                </form>
            </div>
        </div>
        <div class="w-full h-full shadow-md bg-white">
            <form action="#" id="form-vente-commande" class=" w-full h-full p-[7px] flex flex-col">
                <div id="global-message-add-product" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300"></div>
                <fieldset id="container-fieldset" class="w-full flex flex-col pl-[6px] pr-[6px] shadow-md h-full overflow-auto">
                    <div class="flex w-[95%] m-[12px]">
                        <input type="text" class="font-bold text-[13px] w-full" value="Nom">
                        <input type="text" class="font-bold text-center text-[13px] w-[20%]" value="Prix unitaire">
                        <input type="text" class="font-bold text-center text-[13px] w-[14%]" value="Quantite">
                        <input type="text" class="font-bold text-center text-[13px] w-[20%]" value="Total">
                    </div>
                    <hr class="w-full text-gray-300">

                </fieldset>
                <fieldset class="w-full items-center">
                    <div class="w-full justify-between flex h-[30px] items-center">
                        <span class="w-full text-[14px] font-bold text-center">Total Fature</span><p class="text-center w-full text-[15px] font-bold"><span id="totalgeneral">0</span><span> CFA</span></p>
                        <input type="text" id="inputtotalgeneral" name="inputtotalgeneral" value="0" class="hidden">
                        <input type="text" id="index" name="index" class="hidden">
                        <input type="text" id="shopnameinput" name="shopnameinput" class="hidden">
                        <input type="text" id="shopmatricule" name="shopmatricule" class="hidden">
                        <input type="text" id="type_structure" name="type_structure" class="hidden">
                    </div>
                    <button class="w-full cursor-pointer bg-blue-700 text-white text-[14px] h-[35px] hover:bg-blue-800" type="submit">Valider la commande</button>
                </fieldset>
            </form>
        </div>
        <div id="container-display" class="overflow-auto hidden absolute bg-[rgba(0,0,0,0.7)] w-screen h-screen place-items-center grid">
            <span id="fermerDisplayProduct" class="h-[30px] py-[3px] px-[7px] bg-red-600 rounded-md cursor-pointer absolute top-[80px] right-[180px] text-white text-[13px] font-bold">Fermer</span>
            <div id="displayProductSentByName" class="w-[50%] h-[60%] bg-white rounded-md shadow-md">

            </div>
        </div>
    </div>
</body>
</html>
