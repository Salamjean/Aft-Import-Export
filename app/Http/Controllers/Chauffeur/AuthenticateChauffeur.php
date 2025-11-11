<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Chauffeur;
use Exception;
use Illuminate\Http\Request;

class AuthenticateChauffeur extends Controller
{
    public function login(){
        if (auth('chauffeur')->check()) {
            return redirect()->route('chauffeur.dashboard');
        }
        return view('chauffeur.auth.login');
    }

    public function handleLogin(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'email' => 'required|exists:chauffeurs,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Récupérer l'chauffeur par son email
            $chauffeur = Chauffeur::where('email', $request->email)->first();

            // Vérifier si l'chauffeur est archivé
            if ($chauffeur && $chauffeur->archived_at !== null) {
                return redirect()->back()->with('error', 'Votre compte a été supprimé. Vous ne pouvez pas vous connecter.');
            }

            if (auth('chauffeur')->attempt($request->only('email', 'password'))) {
                return redirect()->route('chauffeur.dashboard')->with('success', 'Bienvenue sur la page des demandes en attente');
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la connexion.');
        }
    }
}
