<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_role extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // explicite, même si c'est par défaut
    public $incrementing = true; // auto-increment
    protected $keyType = 'int'; // si c'est un int


    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'role_matricule',
        'user_matricule',
        'responsable_matricule',
        'structure_matricule',
        'fonction',
        'gestion_stock',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
