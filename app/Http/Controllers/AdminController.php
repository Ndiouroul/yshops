<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View; // Utilisez cette classe
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Affiche la vue de la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showAdmin(): View
    {
        return view('admin');
    }
}
