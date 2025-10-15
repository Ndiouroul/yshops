<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\All_product;
use App\Models\Commande;
use App\Models\Online_product;
use App\Models\Structure;
use App\Models\User_role;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

// Utilisez cette classe

class ToupdateprofilController extends Controller
{
    /**
     * Affiche la vue de la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function ShowToupdateprofil()
    {
        return view('toupdateprofil');
    }

}
