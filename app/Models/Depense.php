<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'depense_matricule',
        'charge_matricule',
        'ingredient',
        'quantite',
        'prix',
        'total',
        'structure_matricule',
        'responsable_matricule',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
