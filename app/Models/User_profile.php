<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_profile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';
    protected $primaryKey = 'id';
    public $incrementing = true; // true si clé primaire auto-incrémentée
    protected $keyType = 'int'; // 'string' si user_matricule

    // Champs qui peuvent être remplis en masse (create, update)
    protected $fillable = [
        'profile_matricule',
        'user_matricule',
        'prenom',
        'nom',
        'telephone1',
        'telephone2',
        'adresse',
        'profil',
        'photo_profil',
        'date_naissance',
        'sexe',
        'cni',
        'email',
    ];

    // Si tu veux préciser la table explicitement (optionnel si table = products)
    // protected $table = 'products';
    public $timestamps = true;
}
