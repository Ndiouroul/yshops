<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Online_product extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'product_matricule_online',
        'product_matricule',
        'structure_matricule',
        'facture_matricule',
        'prix_achat',
        'prix_vente',
        'quantite',
        'marge',
        'image',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
