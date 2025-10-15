<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class All_product extends Model
{
    use HasFactory;


    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'product_matricule',
        'structure_matricule',
        'code_barre',
        'nom_produit',
        'responsable_matricule',
        'description',
        'image',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
