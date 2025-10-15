<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-store-connexion" content="{{ route('store_connexion') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css','resources/js/connexion-script.js'])
    <title>Connexion</title>
</head>
<body>
    <div class="bg-white shadow-md px-5 py-1 flex justify-between items-center fixed w-full">
        <p class="text-2xl font-bold text-blue-600">Y-Shops</p>
        <div class="flex space-x-6 items-center">
            <a href="{{route('accueil')}}" class="text-drkgray-700 hover:text-blue-600 text-[13px]">Accueil</a>
            @if(Auth::check())
                <a href="{{route('dashboard')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Dashboard</a>
                <a href="{{route('deconnexion')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Deconnexion</a>
                <p class="text-drkgray-700 hover:text-blue-600 text-[13px] font-bold text-blue-600">{{ Auth::user()->pseudo }} </p>
                <a href="{{route('admin')}}" class="text-drkgray-700 hover:text-blue-600 text-[13px]">Admin</a>
            @else
                <a href="{{route('login')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px] font-bold">Connexion</a>
                <a href="{{route('inscription')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Inscription</a>
            @endif
        </div>
    </div>
    <div class="w-full h-screen grid place-items-center bg-gray-100 py-25 overflow-auto">
        <form id="connexionForm" class="bg-white px-8 py-6 rounded-lg shadow-lg max-w-md w-full">
        @csrf <!-- Protection CSRF obligatoire dans Laravel -->

            <!-- Titre -->
            <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Connexion</h2>

            <!-- Message global -->
            <div id="global-message" class="hidden text-green-700 font-bold mb-4 text-center bg-green-50 p-1 rounded border border-green-300">
            </div>

            <!-- Champ Pseudo -->
            <div class="mb-4">
                <label for="pseudo" class="block mb-2 font-semibold text-gray-600">Pseudo</label>
                <input id="pseudo" type="text" name="pseudo" value="{{ old('pseudo') }}"
                       class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                <span id="pseudo-error" class="text-red-600 text-sm mt-1 block"></span>
            </div>

            <!-- Champ Password -->
            <div class="mb-4">
                <label for="password" class="block mb-2 font-semibold text-gray-600">Mot de passe</label>
                <input id="password" type="password" name="password"
                       class="w-full h-7 px-3 border border-gray-300 rounded-md focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                <span id="password-error" class="text-red-600 text-sm mt-1 block"></span>
            </div>

            <!-- Bouton -->
            <button type="submit"
                    class="w-full cursor-pointer relative bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md text-lg font-semibold transition duration-300">
                <div id="loader" class="hidden absolute inset-0 flex items-center justify-center bg-transparent bg-opacity-30 z-50">
                    <div class="w-8 h-8 border-4 border-white relative left-[-75px] border-dashed rounded-full animate-spin"></div>
                </div>
                S'inscrire
            </button>
        </form>
    </div>
</body>
</html>
