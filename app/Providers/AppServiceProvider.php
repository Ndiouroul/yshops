<?php

namespace App\Providers;

use App\Models\Accreditation;
use App\Models\Structure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 👉 Directive Blade
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->fonction === $role;
        });

        Blade::if('proprietaire', function (){
            return auth()->check() && auth()->user()->fonction !== 'proprietaire';
        });

//        Blade::if('type', function ($type, ){
//            $fonction = auth()->user()->fonction;
//            if($fonction === "proprietaire"){
//                return Structure::where('type_structure', )
//            }
//        })

        Blade::if('gestionStock', function (){
            if(Auth::user()->fonction === 'proprietaire'){
                return true;
            }else{
                return Accreditation::where('agent_matricule', Auth::user()->user_matricule)
                    ->where('gestion_stock', 'ok')
                    ->exists();
            }
        });

        Blade::if('manager', function (){
            if(Auth::user()->fonction === 'proprietaire'){
                return true;
            }else{
                return Accreditation::where('agent_matricule', Auth::user()->user_matricule)
                    ->where('role', 'gerant')
                    ->where('statut', 'actif')
                    ->exists();
            }
        });

        Blade::if('managerall', function (){
            if(Auth::user()->fonction === 'proprietaire'){
                return true;
            }else{
                return Accreditation::where('agent_matricule', Auth::user()->user_matricule)
                    ->where('role', 'gerant')
                    ->where('responsabilite', 'all')
                    ->where('statut', 'actif')
                    ->exists();
            }
        });

        Blade::if('vente', function (){
            return auth()->check() && auth()->user()->fonction !== 'simple-user';
        });

        // 👉 Exemple de Gate
        Gate::define('manage-users', function ($user) {
            return $user->fonction === 'dashwood';
        });
    }
}
