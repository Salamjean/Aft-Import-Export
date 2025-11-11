<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Models\Conteneur;
use App\Models\User;
use App\Services\GroupEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
   public function client(Request $request)
    {
        // Récupérer les conteneurs distincts qui ont des colis
        $conteneurs = Conteneur::whereHas('colis')->get();
        
        // Récupérer les utilisateurs qui ont au moins un colis (en tant qu'expéditeur)
        $users = User::whereHas('colis')->withCount('colis')->get();
        
        return view('admin.client.client', compact('conteneurs', 'users'));
    }

   public function prospect(Request $request)
    {
        // Récupérer les conteneurs distincts qui ont des colis
        $conteneurs = Conteneur::whereHas('colis')->get();
        
        // Récupérer les utilisateurs qui n'ont JAMAIS envoyé de colis
        $users = User::whereDoesntHave('colis')->get();
        
        return view('admin.client.prospect', compact('conteneurs', 'users'));
    }

    
}
