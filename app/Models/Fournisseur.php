<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'fournisseur_matricule',
        'structure_matricule',
        'responsable_matricule',
        'nom_fournisseur',
        'sigle_fournisseur',
        'adresse',
        'email',
        'telephone1',
        'telephone2',
        'telephone_fixe',
        'logo',
        'description',
        'nom-agent',
        'telephone-agent',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
