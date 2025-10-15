<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css','resources/js/admin-script.js'])
    <title>Admin</title>
</head>
<body>
    <div class="bg-white shadow-md px-5 py-1 flex justify-between items-center fixed w-full">
        <p class="text-2xl font-bold text-blue-600">Y-Shops</p>
        <div class="flex space-x-6 items-center">
            <a href="{{route('accueil')}}" class="text-drkgray-700 hover:text-blue-600 text-[13px]">Accueil</a>
            <a href="{{route('dashboard')}}" class="text-drkgray-700 hover:text-blue-600 text-[13px]">Dashboard</a>
            <a href="{{route('inscription')}}" class="text-drkgray-700 hover:text-blue-600 text-[13px]">Inscription</a>
            <a href="{{route('admin')}}" class="text-drkgray-700 hover:text-blue-600 text-[13px]">Admin</a>

            @if(Auth::check())
                <a href="{{route('shop')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Shop</a>
                <a href="{{route('deconnexion')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Deconnexion</a>
                <p class="text-drkgray-700 hover:text-blue-600 text-[13px] font-bold text-blue-600">{{ Auth::user()->pseudo }} </p>
            @else
                <a href="{{route('login')}}" class="text-darkgray-700 hover:text-blue-600 text-[13px]">Connexion</a>

            @endif
        </div>
    </div>
</body>
</html>
