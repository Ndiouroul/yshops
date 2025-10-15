<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewBillTrasformation extends FormRequest
{
    public function authorize(): bool
    {
        return true; // true si tout utilisateur peut faire cette action
    }

    public function rules(): array
    {
        return [
            'numero_facture'       => 'required|string|max:50',
            'total_facture'     => 'required|integer|max:99999999999',
            'facture' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:3096',
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
            'integer' => 'Le champ :attribute doit être un nombre entier.',
            'total_facture.max' => 'Le montant de la facture ne peut pas dépasser :max.',

            // Fichier
            'file'     => 'Le champ :attribute doit être un fichier.',
            'mimes'    => 'Le champ :attribute doit être un fichier au format PDF.',
            'facture.max' => 'La facture ne doit pas dépasser 3 Mo.',
            'facture.required' => 'La facture est obligatoire.',

            'date_facture.required' => 'La date de la facture est obligatoire.',
            'date_facture.date' => 'La date de la facture doit être une date valide.',
        ];
    }
}
