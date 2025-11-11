<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Agent;
use App\Models\ResetCodePasswordAgent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthenticateAgent extends Controller
{
    public function login(){
        if (auth('agent')->check()) {
            return redirect()->route('agent.dashboard');
        }
        return view('agent.auth.login');
    }

        public function handleLogin(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'email' => 'required|exists:agents,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Récupérer l'agent par son email avec seulement les champs nécessaires
            $agent = Agent::where('email', $request->email)
                ->select('id', 'email', 'password', 'archived_at', 'agence_id')
                ->first();

            // Vérifier si l'agent est archivé
            if ($agent && $agent->archived_at !== null) {
                return redirect()->back()->with('error', 'Votre compte a été supprimé. Vous ne pouvez pas vous connecter.');
            }

            if (auth('agent')->attempt($request->only('email', 'password'))) {
                // Récupérer l'agence directement via une requête séparée (plus sécurisé)
                $agence = Agence::where('id', auth('agent')->user()->agence_id)
                    ->select('id', 'pays')
                    ->first();

                // Vérifier si le pays est France ou Chine
                if ($agence && in_array($agence->pays, ['France', 'Chine'])) {
                    return redirect()->route('agent.dashboard')->with('success', 'Bienvenue sur la page des demandes en attente');
                } else {
                    // Pour tous les autres pays (y compris Côte d'Ivoire)
                    return redirect()->route('agent.cote.dashboard')->with('success', 'Bienvenue sur la page des demandes en attente');
                }
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            Log::error('Erreur de connexion agent: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la connexion.');
        }
    }
}
