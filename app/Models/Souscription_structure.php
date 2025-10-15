<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Souscription_structure extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'souscription_matricule',
        'structure_matricule',
        'responsable_matricule',
        'date_souscription',
        'package_matricule',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
