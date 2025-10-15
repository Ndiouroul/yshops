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
<div class="absolute inset-0 w-full h-full bg-[rgba(100,100,100,0.5)] flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-md shadow-lg text-center w-[300px]">
        <p class="text-gray-800 font-semibold mb-4 text-[14px]">
            Veuillez mettre à jour votre profil avant d'accéder à la page de vente.
        </p>
        <a href="{{ route('dashboard') }}"
           class="bg-blue-600 text-white px-5 py-2 rounded-md text-[13px] font-bold hover:bg-blue-700 transition">
            Mettre à jour mon profil
        </a>
    </div>
</div>

</body>
</html>
