<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table des packages pour les structures
        Schema::create('package_structures', function (Blueprint $table) {
            $table->id();
            $table->string('package_matricule', 100);
            $table->string('nom', 50);
            $table->decimal('prix', 10, 2);
            $table->enum('type_package', ['gratuit', 'payant'])->default('gratuit');
            $table->enum('statut', ['actif', 'expire'])->default('actif');
            $table->integer('nombre_produit');
            $table->integer('nombre_boutique');
            $table->integer('nombre_image_produit');
            $table->integer('nombre_vente');
            $table->integer('nombre_abonnement');
            $table->timestamps();
        });

        // Insertion des packages pour les structures
        DB::table('package_structures')->insert([
            [
                'package_matricule' => 'PKG_STR_CLASSIC',
                'nom' => 'Classic',
                'prix' => 0,
                'type_package' => 'gratuit',
                'statut' => 'actif',
                'nombre_boutique' => 1,
                'nombre_image_produit' => 2,
                'nombre_produit' => 2,
                'nombre_vente' => 100,
                'nombre_abonnement' => 25,
            ],
            [
                'package_matricule' => 'PKG_STR_SUPER',
                'nom' => 'Super',
                'prix' => 5000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_boutique' => 2,
                'nombre_image_produit' => 5,
                'nombre_produit' => 10,
                'nombre_vente' => -1, // avantage clair par rapport à Classic
                'nombre_abonnement' => 100,
            ],
            [
                'package_matricule' => 'PKG_STR_EXTRA',
                'nom' => 'Extra',
                'prix' => 10000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_boutique' => 4,
                'nombre_image_produit' => 7,
                'nombre_produit' => 50, // bien plus que 2x Super
                'nombre_vente' => -1,
                'nombre_abonnement' => 1000,
            ],
            [
                'package_matricule' => 'PKG_STR_DIAMOND',
                'nom' => 'Diamond',
                'prix' => 25000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_boutique' => 10,
                'nombre_image_produit' => 10,
                'nombre_produit' => 250,
                'nombre_vente' => -1, // illimité
                'nombre_abonnement' => -1, // illimité
            ],
            [
                'package_matricule' => 'PKG_STR_ELITE',
                'nom' => 'Elite',
                'prix' => 50000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_boutique' => 15,
                'nombre_image_produit' => 10,
                'nombre_produit' => -1, // illimité
                'nombre_vente' => -1, // illimité
                'nombre_abonnement' => -1, // illimité
            ],
            [
                'package_matricule' => 'PKG_STR_SUPER_ELITE',
                'nom' => 'Super Elite',
                'prix' => 100000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_boutique' => 100,
                'nombre_image_produit' => 15,
                'nombre_produit' => -1, // illimité
                'nombre_vente' => -1, // illimité
                'nombre_abonnement' => -1, // illimité
            ],
        ]);

        // Table des packages pour les livreurs
        Schema::create('package_livreurs', function (Blueprint $table) {
            $table->id();
            $table->string('package_matricule', 100);
            $table->string('nom', 50);
            $table->decimal('prix', 10, 2);
            $table->enum('type_package', ['gratuit', 'payant'])->default('gratuit');
            $table->string('statut', 15)->default('actif');
            $table->integer('nombre_notification')->nullable();
            $table->integer('nombre_abonnement')->nullable();
            $table->integer('nombre_livraison')->nullable();
            $table->timestamps();
        });

        // Insertion des packages pour les livreurs
        DB::table('package_livreurs')->insert([
            [
                'package_matricule' => 'PKG_LIV_CLASSIC',
                'nom' => 'Classic',
                'prix' => 0,
                'type_package' => 'gratuit',
                'statut' => 'actif',
                'nombre_notification' => 20,
                'nombre_abonnement' => 2,
                'nombre_livraison' => 10,
            ],
            [
                'package_matricule' => 'PKG_LIV_STARTER',
                'nom' => 'Starter',
                'prix' => 2500,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_notification' => 100,
                'nombre_abonnement' => 10,
                'nombre_livraison' => 100,
            ],
            [
                'package_matricule' => 'PKG_LIV_PRO',
                'nom' => 'Pro',
                'prix' => 5000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_notification' => 500,
                'nombre_abonnement' => 50,
                'nombre_livraison' => 500,
            ],
            [
                'package_matricule' => 'PKG_LIV_BUSINESS',
                'nom' => 'Business',
                'prix' => 10000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_notification' => -1, // illimité
                'nombre_abonnement' => 200,
                'nombre_livraison' => 2000,
            ],
            [
                'package_matricule' => 'PKG_LIV_DIAMOND',
                'nom' => 'Diamond',
                'prix' => 20000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_notification' => -1, // illimité
                'nombre_abonnement' => 1000,
                'nombre_livraison' => 10000,
            ],
            [
                'package_matricule' => 'PKG_LIV_ELITE',
                'nom' => 'Elite',
                'prix' => 50000,
                'type_package' => 'payant',
                'statut' => 'actif',
                'nombre_notification' => -1, // illimité
                'nombre_abonnement' => -1, // illimité
                'nombre_livraison' => -1, // illimité
            ],
        ]);

        Schema::create('souscription_structures', function (Blueprint $table){
            $table->id();
            $table->string('souscription_matricule', 100);
            $table->string('structure_matricule', 100);
            $table->string('responsable_matricule', 100);
            $table->enum('statut', ['actif', 'expire', 'en_attente'])->default('actif');
            $table->date('date_fin_souscription')->nullable();
            $table->string('package_matricule', 100);
            $table->decimal('solde_restant')->nullable();
            $table->timestamps();

            // $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
            // $table->foreign('responsable_matricule')->references('user_matricule')->on('users');
            // $table->foreign('package_matricule')->references('package_matricule')->on('package_structures');
        });

        Schema::create('souscription_livreurs', function (Blueprint $table){
            $table->id();
            $table->string('souscription_matricule', 100);
            $table->string('livreur_matricule', 100);
            $table->enum('statut', ['actif', 'expire'])->default('actif');
            $table->string('date_souscription');
            $table->string('package_matricule', 100);
            $table->timestamps();

            // $table->foreign('livreur_matricule')->references('livreur_matricule')->on('livreurs');
            // $table->foreign('package_matricule')->references('package_matricule')->on('package_livreurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_structures');
        Schema::dropIfExists('package_livreurs');
    }
};
