<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // explicite, même si c'est par défaut
    public $incrementing = true; // auto-increment
    protected $keyType = 'int'; // si c'est un int

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'user_matricule',
        'structure_matricule',
        'type_structure',
        'nom_structure',
        'sigle_structure',
        'logo',
        'telephone1',
        'telephone2',
        'telephone_fixe',
        'adresse_structure',
        'email_structure',
        'description',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
