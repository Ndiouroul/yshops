<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ici tu peux gérer l'autorisation si besoin (ex: return auth()->user()->isAdmin())
        return true;
    }

    public function rules(): array
    {
        return [
            'code_barre'            => ['required', 'string', 'max:100'],
            'prix'            => ['required', 'string', 'max:15'],
            'nom_produit'           => ['required', 'string', 'max:100', 'unique:all_products'],
            'description'           => ['required', 'string'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [

//             code_barre
            'code_barre.string' => 'Le code-barres doit être une chaîne de caractères.',
            'code_barre.max'    => 'Le code-barres ne doit pas dépasser 100 caractères.',
            'code_barre.required'    => 'Le code-barre est requis pour chaque produit.',

            'prix.required' => 'Le prix de vente du produit est requis',
            'prix.max' => 'Le prix de vente ne peut pas depasser 15 chiffres',

            // nom_produit
            'nom_produit.required' => 'Le nom du produit est obligatoire.',
            'nom_produit.string'   => 'Le nom du produit doit être une chaîne de caractères.',
            'nom_produit.max'      => 'Le nom du produit ne doit pas dépasser 100 caractères.',
            'nom_produit.unique'      => 'Ce nom est deja associe un autre produit.',


            // description
            'description.required' => 'La description du produit est obligatoire.',
            'description.string'   => 'La description doit être une chaîne de caractères.',

            // image
            'image.required'    => 'Un image du produit est obligatoire.',
            'image.image'    => 'Le fichier fourni doit être une image.',
            'image.mimes'    => 'L’image doit être au format : jpg, jpeg, png ou webp.',
            'image.max'      => 'L’image ne doit pas dépasser 2 Mo.'
        ];
    }
}
