<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestProductOnUpdate extends FormRequest
{
    public function authorize(): bool
    {
        // Ici tu peux gérer l'autorisation si besoin (ex: return auth()->user()->isAdmin())
        return true;
    }

    public function rules(): array
    {
        return [

            'facture_matricule' => ['string'],
            'prix_achat' => ['required', 'numeric', 'decimal:2'],
            'prix_vente' => ['required', 'numeric', 'decimal:2'],
            'marge' => ['required', 'numeric', 'decimal:2', 'min:1', 'max:99.99'],
            'quantite' => ['integer'],
            'image' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'facture_matricule.string' => "Le matricule de la facture doit être une chaîne de caractères.",

            'prix_achat.required' => "Le prix d'achat est requis.",
            'prix_achat.numeric' => "Le prix d'achat doit être un nombre.",
            'prix_achat.decimal' => "Le prix d'achat doit avoir 2 chiffres après la virgule.",

            'prix_vente.required' => "Le prix de vente est requis.",
            'prix_vente.numeric' => "Le prix de vente doit être un nombre.",
            'prix_vente.decimal' => "Le prix de vente doit avoir 2 chiffres après la virgule.",

            'marge.required' => "La marge est requise.",
            'marge.numeric' => "La marge doit être un nombre.",
            'marge.decimal' => "La marge doit avoir 2 chiffres après la virgule.",
            'marge.min' => "La marge doit être d'au moins 1.",
            'marge.max' => "La marge ne doit pas dépasser 99.99.",

            'quantite.integer' => "La quantité doit être un entier.",

            'image.image' => "Le fichier doit être une image.",
            'image.mimes' => "L'image doit être au format jpg, jpeg, png ou webp.",
            'image.max' => "L'image ne doit pas dépasser 2 Mo.",
        ];
    }
}
