<?php

namespace App\Http\Controllers\Admin\Colis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditColisController extends Controller
{
    public function edit($id)
{
    try {
        // Récupérer le colis avec toutes ses relations
        $colis = Colis::with([
            'conteneur',
            'agenceExpedition', 
            'agenceDestination',
            'service'
        ])->findOrFail($id);

        // Récupérer les agences d'expédition (pays != Côte d'Ivoire)
        $agencesExpedition = Agence::where('pays', '!=', 'Côte d\'Ivoire')->get();

        // Récupérer les services
        $services = Service::all();

        // Récupérer les produits
        $produits = Produit::with('agenceDestination')->get();

        // Récupérer le mode de transit actuel
        $modeTransit = $colis->mode_transit;

        // Récupérer l'utilisateur connecté pour les initiales
        $user = Auth::guard('admin')->user();
        $initiales = strtoupper(substr($user->name, 0, 2));

        // Générer la référence (même logique que create mais avec les données existantes)
        $reference = $this->genererReference(
            $initiales, 
            $modeTransit, 
            $colis->agence_expedition_id
        );

        // Décoder les détails des colis
        $colisDetails = json_decode($colis->colis, true) ?? [];

        return view('admin.colis.edit', compact(
            'colis',
            'conteneur', 
            'agencesExpedition', 
            'reference', 
            'services', 
            'produits',
            'modeTransit',
            'colisDetails'
        ));

    } catch (\Exception $e) {
        \Log::error('Erreur lors de la récupération du colis pour édition: ' . $e->getMessage());
        
        return redirect()->route('colis.index')
            ->with('error', 'Colis non trouvé ou erreur lors du chargement: ' . $e->getMessage());
    }
}
}
