<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewBill extends FormRequest
{
    public function authorize(): bool
    {
        return true; // true si tout utilisateur peut faire cette action
    }

    public function rules(): array
    {
        return [
            'numero_facture'       => 'required|string|max:50',
            'total_facture'     => 'required|numeric|max:99999999999',
            'facture' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:3096',
            'fournisseur' => 'required',
            'marge' => 'required|numeric|between:1,99.99',
            'date_facture' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',

            // Texte
            'string'   => 'Le champ :attribute doit être une chaîne de caractères.',
            'numero_facture.max' => 'Le numéro de facture ne peut pas dépasser :max caractères.',

            // Numérique
            'numeric' => 'Le champ :attribute doit être un nombre.',
            'total_facture.max' => 'Le montant de la facture ne peut pas dépasser :max.',

            // Fichier
            'file'     => 'Le champ :attribute doit être un fichier.',
            'mimes'    => 'Le champ :attribute doit être un fichier au format PDF.',
            'facture.max' => 'La facture ne doit pas dépasser 5 Mo.',
            'facture.required' => 'La facture est obligatoire.',

            'fournisseur.required' => 'Le nom du fournisseur est obligatoire',

            'marge.required' => 'La marge est obligatoire.',
            'marge.numeric'  => 'La marge doit être un nombre valide.',
            'marge.decimal'  => 'La marge doit être un nombre avec au maximum 2 décimales.',
            'marge.between'  => 'La marge doit être comprise entre 1 et 99.',

            'date_facture.required' => 'La date de la facture est obligatoire.',
            'date_facture.date' => 'La date de la facture doit être une date valide.',
        ];
    }
}
