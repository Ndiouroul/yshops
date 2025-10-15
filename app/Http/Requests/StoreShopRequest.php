<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // true si l'utilisateur peut faire cette action
    }

    public function rules(): array
    {
        return [
            'nom_structure' => 'required|string|max:255',
            'sigle_structure' => 'nullable|string|min:1|max:10',
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'telephone1' => 'required|string|max:15',
            'telephone2' => 'nullable|string|min:5|max:15',
            'telephone_fixe' => 'nullable|string|min:5|max:15',
            'adresse_structure' => 'required|string|max:255',
            'email_structure' => 'required|max:100',
            'description' => 'required|min:25',
            'type_structure'=>'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'nom_structure.required' => 'Le nom de la structure est requis',
            'sigle_structure.min' => 'Le sigle doit contenir au moins 1 caractère',
            'sigle_structure.max' => 'Le sigle ne peut pas dépasser 10 caractères',
            'telephone1.required' => 'Le téléphone principal est requis',
            'telephone1.max' => 'Le téléphone principal ne peut pas dépasser 15 caractères',
            'telephone2.min' => 'Le téléphone secondaire doit contenir au moins 7 caractères',
            'telephone2.max' => 'Le téléphone secondaire ne peut pas dépasser 15 caractères',
            'telephone_fixe.min' => 'Le téléphone fixe doit contenir au moins 7 caractères',
            'telephone_fixe.max' => 'Le téléphone fixe ne peut pas dépasser 15 caractères',
            'adresse_structure.required' => 'L’adresse mail est requise pour creer la boutique',
            'adresse_structure.max' => 'L’adresse ne peut pas dépasser 255 caractères',
            'email_structure.email' => 'L’email n’est pas valide',
            'email_structure.required' => "L'adresse mail est requise",
            'email_structure.max' => 'L’email ne peut pas dépasser 100 caractères',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.mimes' => 'Le logo doit être au format : jpeg, png, jpg ou gif.',
            'logo.max'   => 'Le logo ne doit pas dépasser 2 Mo.',
            'logo.required'   => 'Le logo est obligatoire.',
            'description.required'=> 'La description est obligatoire',
            'description.min' => 'La description doit contenir au moins 25 carateres',
            'type_structure.required' => "Le type de la structure est obligatoire",
        ];
    }

}
