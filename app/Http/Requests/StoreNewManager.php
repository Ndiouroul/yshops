<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewManager extends FormRequest
{
    public function authorize(): bool
    {
        return true; // true si l'utilisateur peut faire cette action
    }

    public function rules(): array
    {
        return [
            'pseudo' => 'required|string|min:5|max:30|unique:users,pseudo|regex:/^[a-zA-Z0-9_]+$/',
            'telephone1' => 'required|string|max:15|min:5|unique:users,telephone1',
            'password' => 'required|string|min:7|confirmed',
            'email' => 'required|string|email|max:255|unique:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            // 🎭 Cas pseudo
            'pseudo.required' => 'Le pseudo est obligatoire.',
            'pseudo.string'   => 'Le pseudo doit être une chaîne de caractères.',
            'pseudo.min'      => 'Le pseudo doit contenir au moins 7 caractères.',
            'pseudo.max'      => 'Le pseudo ne doit pas dépasser 30 caractères.',
            'pseudo.unique'   => 'Ce pseudo est déjà utilisé, veuillez en choisir un autre.',
            'pseudo.regex'    => 'Le pseudo ne doit contenir que des lettres, chiffres ou underscore (_).',

            // 📱 Cas téléphone
            'telephone1.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone1.string'   => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone1.min'      => 'Le numéro de téléphone doit contenir au moins 5 caractères.',
            'telephone1.max'      => 'Le numéro de téléphone ne doit pas dépasser 15 caractères.',
            'telephone1.unique'   => 'Ce numéro de téléphone est déjà utilisé.',

            // 📧 Cas email
            'email.required' => 'L’adresse email est obligatoire.',
            'email.string'   => 'L’adresse email doit être une chaîne de caractères.',
            'email.email'    => 'Le format de l’adresse email est invalide.',
            'email.max'      => 'L’adresse email ne doit pas dépasser 255 caractères.',
            'email.unique'   => 'Cette adresse email est déjà utilisée.',

            // 🔐 Cas mot de passe
            'password.required' => '⚠️ Le mot de passe est obligatoire.',
            'password.min'      => '⚠️ Le mot de passe doit contenir au moins 7 caractères.',
            'password.string'   => '⚠️ Le mot de passe doit être une chaîne de caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }

}
