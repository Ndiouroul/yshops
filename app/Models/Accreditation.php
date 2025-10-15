<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accreditation extends Model
{
    use HasFactory;

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'accreditation_matricule',
        'agent_matricule',
        'structure_matricule',
        'responsable_matricule',
        'role',
        'privilege',
        'statut',
        'gestion_stock'
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
