<?php

namespace App\Http\Controllers\Agent\Bateau;

use App\Http\Controllers\Controller;
use App\Models\Bateau;
use App\Models\Conteneur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentBateauController extends Controller
{
    public function planifier(){
        return view('agent.bateau.planifier');
    }

    public function ouvrir(){
        return view('agent.bateau.ouvrir');
    }

   public function index()
    {
        $userAgenceId = Auth::guard('agent')->user()->agence_id;
        
        $planifications = Bateau::with(['conteneur', 'agence'])
            ->where('agence_id', $userAgenceId) // Filtrer directement sur le bateau
            ->orderBy('created_at', 'desc')
            ->paginate(2);

        return view('agent.bateau.index', compact('planifications'));
    }
    public function getConteneursByType($type)
    {
        $userAgenceId =Auth::guard('agent')->user()->agence_id;
        $conteneurs = Conteneur::where('type_conteneur', $type)
                            ->where('statut', 'fermer')
                            ->where('agence_id', $userAgenceId)
                            ->whereNotIn('id', function($query) {
                                $query->select('conteneur_id')
                                    ->from('bateaus')
                                    ->whereNotNull('conteneur_id');
                            })
                            ->select('id', 'name_conteneur', 'numero_conteneur')
                             ->get();
        
        return response()->json($conteneurs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_transport' => 'required|string|in:Bateau,Avion',
            'conteneur' => 'required|string',
            'date_arrive' => 'required|date',
            'compagnie' => 'nullable|string',
            'nom' => 'nullable|string',
            'numero' => 'nullable|string',
            'agence_id' => 'required|exists:agences,id'
        ]);

        // Générer la référence automatiquement
        $reference = $this->genererReference($validated['type_transport']);

        // Créer l'enregistrement
        Bateau::create([
            'type_transport' => $validated['type_transport'],
            'conteneur_id' => $validated['conteneur'],
            'reference' => $reference,
            'date_arrive' => $validated['date_arrive'],
            'compagnie' => $validated['compagnie'],
            'nom' => $validated['nom'],
            'numero' => $validated['numero'],
            'statut' => 'en_cours',
            'agence_id' => $validated['agence_id']
        ]);

        return redirect()->route('agent.bateau.index')->with('success', 'Transport planifié avec succès! Référence: ' . $reference);
    }

    /**
     * Génère une référence automatique selon le type de transport
     */
    private function genererReference($typeTransport)
    {
        // Déterminer le préfixe selon le type de transport
        $prefixe = $typeTransport === 'Bateau' ? 'BT' : 'AV';
        
        // Récupérer les 4 premières lettres du mois en cours (en français)
        $mois = $this->getMoisAbrege();
        
        // Générer un numéro aléatoire de 6 chiffres
        $numero = mt_rand(100000, 999999);
        
        // Vérifier si la référence existe déjà (au cas où)
        $reference = $prefixe . '-' . $mois . '-' . $numero;
        
        // S'assurer que la référence est unique
        while (Bateau::where('reference', $reference)->exists()) {
            $numero = mt_rand(100000, 999999);
            $reference = $prefixe . '-' . $mois . '-' . $numero;
        }
        
        return $reference;
    }

    /**
     * Retourne les 4 premières lettres du mois en cours en français
     */
    private function getMoisAbrege()
    {
        $moisFrancais = [
            'Janvier' => 'JANV',
            'Février' => 'FEVR',
            'Mars' => 'MARS',
            'Avril' => 'AVRI',
            'Mai' => 'MAI', // Note: "MAI" a seulement 3 lettres
            'Juin' => 'JUIN',
            'Juillet' => 'JUIL',
            'Août' => 'AOUT',
            'Septembre' => 'SEPT',
            'Octobre' => 'OCTO',
            'Novembre' => 'NOVE',
            'Décembre' => 'DECE'
        ];
        
        $moisEnCours = now()->format('F');
        
        // Si le mois est Mai, on retourne "MAI" directement
        if ($moisEnCours === 'May') {
            return 'MAI';
        }
        
        // Traduire le mois anglais en français et prendre les 4 premières lettres
        $traduction = [
            'January' => 'Janvier',
            'February' => 'Février',
            'March' => 'Mars',
            'April' => 'Avril',
            'May' => 'Mai',
            'June' => 'Juin',
            'July' => 'Juillet',
            'August' => 'Août',
            'September' => 'Septembre',
            'October' => 'Octobre',
            'November' => 'Novembre',
            'December' => 'Décembre'
        ];
        
        $moisFrancaisComplet = $traduction[$moisEnCours] ?? 'SEPT';
        
        return $moisFrancais[$moisFrancaisComplet] ?? 'SEPT';
    }
    
    // Méthode pour afficher le conteneur associé au bateau
    public function showConteneur($id)
    {
        // Récupérer le bateau avec les relations conteneur et agence
        $bateau = Bateau::with(['conteneur', 'agence'])->findOrFail($id);
        
        // Vérifier si le bateau a un conteneur associé
        if (!$bateau->conteneur) {
            return redirect()->back()->with('error', 'Aucun conteneur associé à ce bateau.');
        }

        return view('agent.bateau.ouvrir', compact('bateau'));
    }

    // Méthode pour supprimer
    public function destroy($id)
    {
        try {
            $bateau = Bateau::findOrFail($id);
            $bateau->delete();
            
            return response()->json(['success' => true, 'message' => 'Planification supprimée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
