<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'entree_matricule',
        'commande_matricule',
        'product_matricule',
        'prix',
        'quantite',
        'total',
        'user_matricule',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
