<?php

namespace App\Http\Controllers\Agent\Client;

use App\Http\Controllers\Controller;
use App\Models\Conteneur;
use App\Models\User;
use Illuminate\Http\Request;

class AgentClientController extends Controller
{
    public function client(Request $request)
    {
        // Récupérer les conteneurs distincts qui ont des colis
        $conteneurs = Conteneur::whereHas('colis')->get();
        
        // Récupérer les utilisateurs qui ont au moins un colis (en tant qu'expéditeur)
        $users = User::whereHas('colis')->withCount('colis')->get();
        
        return view('agent.client.client', compact('conteneurs', 'users'));
    }

   public function prospect(Request $request)
    {
        // Récupérer les conteneurs distincts qui ont des colis
        $conteneurs = Conteneur::whereHas('colis')->get();
        
        // Récupérer les utilisateurs qui n'ont JAMAIS envoyé de colis
        $users = User::whereDoesntHave('colis')->get();
        
        return view('agent.client.prospect', compact('conteneurs', 'users'));
    }
}
