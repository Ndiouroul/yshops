<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'vente_matricule',
        'product_matricule',
        'recue_matricule',
        'prix',
        'prix_total',
        'quantite',
        'structure_matricule',
        'gerant_matricule',
        'vendeur_matricule',
        'statut',
        'annule_par',
        'date_vente',

    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}

