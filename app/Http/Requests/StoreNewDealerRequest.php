<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewDealerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // true si l'utilisateur peut faire cette action
    }

    public function rules(): array
    {
        return [
            'nom_fournisseur'       => ['required', 'string', 'max:100'],
            'sigle_fournisseur'     => ['nullable', 'string', 'max:30'],
            'adresse'               => ['nullable', 'string', 'max:255'],
            'email'               => ['nullable', 'string', 'max:255', 'unique:fournisseurs'],
            'telephone1'              => ['required', 'string', 'max:15', 'unique:fournisseurs'],
            'telephone2'              => ['nullable', 'string', 'max:15', 'unique:fournisseurs'],
            'telephone_fixe'          => ['nullable', 'string', 'max:15', 'unique:fournisseurs'],
            'logo'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description'           => ['required', 'string'],
            'nom_agent'             => ['nullable', 'string', 'max:100'],
            'numero_agent'          => ['nullable', 'string', 'max:15', 'unique'], // uniquement chiffres

        ];
    }

    public function messages(): array
    {
        return [
            // nom_fournisseur
            'nom_fournisseur.required' => 'Le nom du fournisseur est obligatoire.',
            'nom_fournisseur.string'   => 'Le nom du fournisseur doit être une chaîne de caractères.',
            'nom_fournisseur.max'      => 'Le nom du fournisseur ne doit pas dépasser 100 caractères.',

            // sigle_fournisseur
            'sigle_fournisseur.string' => 'Le sigle du fournisseur doit être une chaîne de caractères.',
            'sigle_fournisseur.max'    => 'Le sigle du fournisseur ne doit pas dépasser 50 caractères.',

            // adresse
            'adresse.string' => 'L’adresse doit être une chaîne de caractères.',
            'adresse.max'    => 'L’adresse ne doit pas dépasser 255 caractères.',

            // email
            'email.string' => 'L’email doit être une chaîne de caractères.',
            'email.max'    => 'L’email ne doit pas dépasser 255 caractères.',
            'email.unique'    => 'Cette adresse mail est deja associee a un autre fournisseur',
            // si tu ajoutes le rule email: 'email.email' => 'Le format de l’email est invalide.',

            // telephone1
            'telephone1.required' => 'Le telephone principal est obligatoire.',
            'telephone1.string'   => 'Le telephone principal doit être une chaîne de caractères.',
            'telephone1.max'      => 'Le telephone principal ne doit pas dépasser 15 chiffres.',

            'telephone1.unique'    => 'Ce numero de telephone principal est deja attribue a un autre fournisseur.',

            // telephone2
            'telephone2.string' => 'Le telephone secondaire doit être une chaîne de caractères.',
            'telephone2.max'    => 'Le telephone secondaire ne doit pas dépasser 15 chiffres.',

            'telephone2.unique'    => 'Ce numero de telephone secondaire est deja attribue a un autre fournisseur.',


            // telephone_fixe
            'telephone_fixe.string' => 'Le telephone fixe doit être une chaîne de caractères.',
            'telephone_fixe.max'    => 'Le telephone fixe ne doit pas dépasser 15 chiffres.',

            'telephone_fixe.unique'    => 'Ce numero de telephone fixe est deja attribue a un autre fournisseur.',


            // logo
            'logo.image'    => 'Le fichier fourni doit être une image.',
            'logo.mimes'    => 'L’image doit être au format : jpg, jpeg, png ou webp.',
            'logo.max'      => 'L’image ne doit pas dépasser 2 Mo.',

            // description
            'description.required' => 'La description est obligatoire.',
            'description.string'   => 'La description doit être une chaîne de caractères.',

            // nom_agent
            'nom_agent.string' => 'Le nom de l’agent doit être une chaîne de caractères.',
            'nom_agent.max'    => 'Le nom de l’agent ne doit pas dépasser 100 caractères.',

            // numero_agent
            'numero-agent.string' => 'Le numéro de l’agent doit être une chaîne de caractères.',
            'numero-agent.max'    => 'Le numéro de l’agent ne doit pas dépasser 15 chiffres.',

        ];
    }

}
