<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // -----------------------------
        // 1️⃣ UTILISATEURS
        // -----------------------------
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'user_matricule');
            $table->string('pseudo', 50)->unique();
            $table->string('telephone1', 50)->unique();
            $table->enum('fonction', ['proprietaire', 'gerant', 'caissier', 'simple-user']);
        });


        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_matricule',100);
            $table->string('user_matricule',100);
            $table->string('prenom',75)->default('');
            $table->string('nom',50);
            $table->string('telephone1',15)->unique();
            $table->string('telephone2',15)->nullable();
            $table->string('adresse',255);
            $table->string('email',100)->unique();
            $table->enum('profil', ['livreur', 'vendeur', 'simple-user', 'mixte', 'dashwood']);
            $table->string('photo_profil',100)->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('sexe',['H','F'])->nullable();
            $table->string('cni', 50)->nullable()->unique();
            $table->timestamps();

//            $table->foreign('user_matricule')->references('user_matricule')->on('users');
        });

        // -----------------------------
        // 3️⃣ STRUCTURES / BOUTIQUES
        // -----------------------------
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('user_matricule',100);
            $table->string('structure_matricule',100);
            $table->enum('type_structure',['transformation', 'revente']);
            $table->string('categorie_structure', 17)->default('other');
            $table->string('nom_structure',75);
            $table->string('sigle_structure',30)->nullable();
            $table->string('logo',100)->nullable();
            $table->string('telephone1',15);
            $table->string('telephone2',15)->nullable();
            $table->string('telephone_fixe',15)->nullable();
            $table->string('adresse_structure',255);
            $table->string('email_structure',100);
            $table->string('statut',15)->default('actif');
            $table->text('description');
            $table->string('code_qr')->nullable();
            $table->timestamps();
        });

        // -----------------------------
        // 4️⃣ ROLES UTILISATEURS PAR STRUCTURE
        // -----------------------------
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_matricule',100);
            $table->string('user_matricule',100);
            $table->string('responsable_matricule',100)->nullable();
            $table->string('structure_matricule',100);
            $table->enum('fonction',['proprietaire','caissier','gerant', 'gestionnaire_stock', 'dashwood']);
            $table->enum('gestion_stock',['ok', 'none'])->default('none');
            $table->enum('statut', ['actif', 'suspendu'])->default('actif');
            $table->timestamps();

//            $table->foreign('user_matricule')->references('user_matricule')->on('users');
//            $table->foreign('responsable_matricule')->references('user_matricule')->on('users');
//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
        });


        Schema::create('accreditations', function (Blueprint $table){
            $table->id();
            $table->string('accreditation_matricule', 50);
            $table->string('agent_matricule', 50);
            $table->string('responsable_matricule', 50);
            $table->string('structure_matricule', 50);
            $table->enum('role', ['caissier', 'gerant']);
            $table->enum('privilege', ['eleve','normal'])->default('normal');
            $table->enum('gestion_stock',['ok', 'none'])->default('none');
            $table->enum('statut',['actif', 'inactif'])->default('actif');
            $table->timestamps();

//            $table->foreign('user_matricule')->references('user_matricule')->on('users');
//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
//            $table->foreign('role_matricule')->references('role_matricule')->on('user_roles');
        });

        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('fournisseur_matricule',100);
            $table->string('structure_matricule',50);
            $table->string('responsable_matricule',50);
            $table->string('nom_fournisseur',50)->unique();
            $table->string('sigle_fournisseur',50)->nullable();
            $table->string('adresse',255)->nullable();
            $table->string('email',255)->nullable()->unique();
            $table->string('telephone1',15)->unique();
            $table->string('telephone2',15)->nullable()->unique();
            $table->string('telephone_fixe',15)->nullable()->unique();
            $table->string('logo',100)->nullable();
            $table->text('description');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->string('nom_agent',50)->nullable();
            $table->string('telephone_agent',50)->nullable()->unique();
            $table->timestamps();

//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
//            $table->foreign('responsable_matricule')->references('user_matricule')->on('users');
        });

        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('facture_matricule',100);
            $table->string('numero_facture',50);
            $table->decimal('total_facture',10,2);
            $table->string('facture',255);
            $table->decimal('marge',5, 2);
            $table->string('structure_matricule',50);
            $table->string('fournisseur_matricule',50);
            $table->string('responsable_matricule',50);
            $table->timestamps();

//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
//            $table->foreign('fournisseur_matricule')->references('fournisseur_matricule')->on('fournisseurs');
//            $table->foreign('responsable_matricule')->references('user_matricule')->on('users');
        });


        Schema::create('all_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_matricule',100);
            $table->string('structure_matricule',100);
            $table->string('responsable_matricule',100);
            $table->string('code_barre', 100) -> nullable();
            $table->string('nom_produit',75)->unique();
            $table->text('description');
            $table->string('image', 100);
            $table->timestamps();

//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
//            $table->foreign('responsable_matricule')->references('user_matricule')->on('users');
        });


        Schema::create('online_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_matricule_online',100);
            $table->string('product_matricule',100);
            $table->string('structure_matricule',100);
            $table->string('facture_matricule', 100);
            $table->decimal('prix_achat',15,2);
            $table->decimal('prix_vente',15,2);
            $table->integer('quantite')->default(0);
            $table->decimal('marge', 5, 2);
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->text('image');
            $table->date('date_peremption')->nullable();
            $table->timestamps();

//            $table->foreign('product_matricule')->references('product_matricule')->on('all_products');
//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
//            $table->foreign('facture_matricule')->references('facture_matricule')->on('factures');

        });


        Schema::create('recues', function (Blueprint $table){
            $table->id();
            $table->string('recue_matricule', 100);
            $table->string('product_matricule', 100);
            $table->string('structure_matricule', 100);
            $table->string('vente_matricule', 100);
            $table->string('commande_matricule', 100)->nullable();
            $table->string('prix');
            $table->string('quantite');
            $table->string('total');
            $table->string('user_matricule');
            $table->timestamps();

//            $table->foreign('product_matricule')->references('product_matricule')->on('all_products');
//            $table->foreign('user_matricule')->references('user_matricule')->on('users');
        });

        Schema::create('commandes', function (Blueprint $table){
            $table->id();
            $table->string('commande_matricule');
            $table->string('product_matricule');
            $table->string('prix');
            $table->string('quantite');
            $table->string('total');
            $table->string('user_matricule');
            $table->enum('statut', ['valide', 'accepte', 'traitement', 'livraison', 'annule'])->default('valide');
            $table->timestamps();

//            $table->foreign('product_matricule')->references('product_matricule')->on('all_products');
//            $table->foreign('user_matricule')->references('user_matricule')->on('users');
        });


        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('vente_matricule',100);
            $table->string('recue_matricule',50);
            $table->integer('prix_total')->default(0);
            $table->string('structure_matricule',50)->nullable();
            $table->string('vendeur_matricule',50);
            $table->enum('statut',['ras', 'annule'])->nullable()->default('ras');
            $table->string('responsable_annulation', 50)->nullable();
            $table->timestamps();

//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
//            $table->foreign('vendeur_matricule')->references('user_matricule')->on('users');
//            $table->foreign('responsable_annulation')->references('user_matricule')->on('users');
//            $table->foreign('recue_matricule')->references('recue_matricule')->on('recues');
        });

        Schema::create('charges', function (Blueprint $table){
            $table->id();
            $table->string('charge_matricule',100);
            $table->string('numero_facture',50);
            $table->decimal('total_facture',10,2);
            $table->string('facture',255);
            $table->string('structure_matricule',50);
            $table->string('responsable_matricule',50);
            $table->timestamps();

        });

        Schema::create('depenses', function (Blueprint $table){
            $table->id();
            $table->string('depense_matricule',100);
            $table->string('charge_matricule',50);
            $table->string('ingredient_matricule', 50);
            $table->decimal('quantite', 9, 2);
            $table->decimal('prix',15, 2);
            $table->decimal('total',15, 2);
            $table->string('structure_matricule',50);
            $table->string('responsable_matricule',50);
            $table->timestamps();

        });

        Schema::create('ingredients', function (Blueprint $table){
            $table->id();
            $table->string('ingredient_matricule',100);
            $table->string('nom',50);
            $table->string('image',100);
            $table->text('description');
            $table->string('categorie', 17);
            $table->enum('unite_mesure', ['litre', 'kilo', 'm^2', 'm^3', 'other']);
            $table->string('responsable_matricule',50);
            $table->timestamps();

        });

        Schema::create('editings', function (Blueprint $table){
            $table->id();
            $table->string('edit_matricule',100);
            $table->string('edited_matricule',50);
            $table->string('field_edited',50);
            $table->string('table',50);
            $table->string('before_value',255);
            $table->string('after_value',255);
            $table->string('structure_matricule',50);
            $table->string('responsable_matricule',50);
            $table->timestamps();

        });

        Schema::create('livreurs', function (Blueprint $table) {
            $table->id();
            $table->string('livreur_matricule',100);
            $table->string('user_matricule',50);
            $table->string('groupe',50);
            $table->enum('type', ['departemental', 'regionnal', 'nationnal', 'internationnal']);
            $table->timestamps();

//            $table->foreign('user_matricule')->references('user_matricule')->on('users');
        });

        Schema::create('groupe_livreurs', function (Blueprint $table){
            $table->id();
            $table->string('groupe_matricule', 100);
            $table->string('nom_groupe', 30);
            $table->string('adresse_groupe', 30);
            $table->enum('type', ['departemental', 'regionnal', 'nationnal', 'internationnal']);
            $table->string('zone', 50);
            $table->string('responsable', 50);
            $table->timestamps();

//            $table->foreign('responsable')->references('livreur_matricule')->on('livreurs');

        });

        Schema::create('groupage_livreurs', function (Blueprint $table){
            $table->id();
            $table->string('groupage_matricule', 100);
            $table->string('groupe_matricule', 50);
            $table->string('responsable_groupe_matricule', 50);
            $table->string('matricule_livreur', 50);
            $table->date('date_annulation')->nullable();
            $table->timestamps();

//            $table->foreign('groupe_matricule')->references('groupe_matricule')->on('groupe_livreurs');
//            $table->foreign('matricule_livreur')->references('livreur_matricule')->on('livreurs');
        });

        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->string('livraison_matricule',100);
            $table->string('livreur_matricule',50)->nullable();
            $table->enum('statut',['accepte', 'en_attente','en_cours','livre'])->default('en_attente');
            $table->timestamp('date_ajout')->useCurrent();
            $table->timestamp('date_traite')->nullable();
            $table->timestamps();

//            $table->foreign('livreur_matricule')->references('livreur_matricule')->on('livreurs');
        });

        Schema::create('colis', function (Blueprint $table){
            $table->id();
            $table->string('coli_matricule', 100);
            $table->string('produit_matricule',50)->nullable();
            $table->string('client_matricule',50)->nullable();
            $table->string('structure_matricule', 50)->nullable();
            $table->string('vente_matricule', 50)->nullable();
            $table->string('livraison_matricule', 50);
            $table->timestamps();

//            $table->foreign('client_matricule')->references('user_matricule')->on('users');
//            $table->foreign('structure_matricule')->references('structure_matricule')->on('structures');
//            $table->foreign('vente_matricule')->references('vente_matricule')->on('ventes');
//            $table->foreign('livraison_matricule')->references('livraison_matricule')->on('livraisons');
        });


        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->string('abonnement_matricule',100);
            $table->string('user_matricule',50)->nullable();
            $table->string('structure_matricule',50)->nullable();
            $table->timestamps();

//            $table->foreign('abonne_simple_user')->references('user_matricule')->on('users');
//            $table->foreign('abonne_livreur')->references('livreur_matricule')->on('livreurs');
//            $table->foreign('abonne_structure')->references('structure_matricule')->on('structures');
//            $table->foreign('structure_matricule_abonnement')->references('structure_matricule')->on('structures');
//            $table->foreign('user_matricule_abonnement')->references('user_matricule')->on('users');

        });

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->string('aime_matricule',100);
            $table->string('user_matricule',50)->nullable();
            $table->string('structure_matricule',50)->nullable();
            $table->timestamps();

        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->string('vote_matricule',100);
            $table->string('user_matricule',50)->nullable();
            $table->string('structure_matricule',50)->nullable();
            $table->timestamps();

        });

        // -----------------------------
        // 1️⃣3️⃣ EVALUATIONS
        // -----------------------------
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('evaluation_matricule',100);
            $table->string('structure_matricule',50);
            $table->string('user_matricule',50);
            $table->string('auteur_matricule',50);
            $table->integer('note')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();

//            $table->foreign('auteur_matricule')->references('user_matricule')->on('users');
//            $table->foreign('cible_matricule')->references('user_matricule')->on('users');
        });

        // -----------------------------
        // 1️⃣4️⃣ PAIEMENTS
        // -----------------------------
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('paiement_matricule',100);
            $table->string('user_matricule',50);
            $table->enum('payment_operateur', ['orange', 'wave', 'free', 'expresso', 'virement']);
            $table->string('payment_code');
            $table->string('boutique');
            $table->string('package');
            $table->string('duree');
            $table->integer('solde_restant')->default(0);
            $table->timestamps();

//            $table->foreign('user_matricule')->references('user_matricule')->on('users');
        });

        // -----------------------------
        // 1️⃣5️⃣ PACKAGES
        // -----------------------------


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer dans l'ordre inverse des dépendances


        Schema::dropIfExists('paiements');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('abonnements');
        Schema::dropIfExists('colis');
        Schema::dropIfExists('livraisons');
        Schema::dropIfExists('groupage_livreurs');
        Schema::dropIfExists('groupe_livreurs');
        Schema::dropIfExists('livreurs');
        Schema::dropIfExists('factures');
        Schema::dropIfExists('fournisseurs');
        Schema::dropIfExists('ventes');
        Schema::dropIfExists('online_products');
        Schema::dropIfExists('all_products');
        Schema::dropIfExists('accreditations');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('structures');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('depenses');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('editings');

        // Pour la table users : tu avais modifié la structure
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary(['user_matricule']);
            $table->dropColumn(['pseudo', 'telephone1']);
        });

    }
};
