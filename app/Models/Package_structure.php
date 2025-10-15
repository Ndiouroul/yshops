<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package_structure extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'package_matricule',
        'nom',
        'prix',
        'type_package',
        'statut',
        'nombre_produit',
        'nombre_boutique',
        'nombre_image_produit',
        'nombre_vente',
        'nombre_abonnement',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
