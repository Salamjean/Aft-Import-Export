<?php

namespace App\Http\Controllers\Admin\Conteneur;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Conteneur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class ConteneurController extends Controller
{
    public function index()
    {
        $conteneurs = Conteneur::with(['colis.agenceExpedition'])
            ->where('statut','ouvert')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.conteneur.index', compact('conteneurs'));
    }

    public function create(){
        return view('admin.conteneur.create');
    }

    public function store(Request $request)
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'name_conteneur' => 'required|string|max:255|unique:conteneurs,name_conteneur',
                'type_conteneur' => 'required|string|in:Conteneur,Ballon',
                'agence_id' => 'required',
                'numero_conteneur' => 'nullable|string|max:255|unique:conteneurs,numero_conteneur',
            ]);

            // Création du conteneur
            $conteneur = Conteneur::create([
                'name_conteneur' => $validatedData['name_conteneur'],
                'type_conteneur' => $validatedData['type_conteneur'],
                'agence_id' => $validatedData['agence_id'],
                'statut' => 'ouvert',
                'numero_conteneur' => $validatedData['numero_conteneur'] ?? null,
            ]);

            // Redirection avec message de succès
            return redirect()->route('conteneur.index')
                            ->with('success', 'Conteneur "'.$conteneur->name_conteneur.'" créé avec succès!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->validator)
                            ->withInput();
                            
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Une erreur est survenue lors de la création du conteneur.')
                            ->withInput();
        }
    }

    public function edit($id)
    {
        $conteneur = Conteneur::findOrFail($id);
        return view('admin.conteneur.edit', compact('conteneur'));
    }

    public function update(Request $request, $id)
    {
        $conteneur = Conteneur::findOrFail($id);

        // Validation des données
        $validatedData = $request->validate([
            'name_conteneur' => 'required|string|max:255|unique:conteneurs,name_conteneur,' . $id,
            'type_conteneur' => 'required|string|in:Conteneur,Ballon',
            'numero_conteneur' => 'nullable|string|max:255|unique:conteneurs,numero_conteneur,' . $id,
        ], [
            'name_conteneur.required' => 'Le nom du conteneur est obligatoire.',
            'name_conteneur.unique' => 'Ce nom de conteneur existe déjà.',
            'type_conteneur.required' => 'Le type de conteneur est obligatoire.',
            'type_conteneur.in' => 'Le type doit être "Conteneur" ou "Ballon".',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être "ouvert" ou "fermer".',
            'numero_conteneur.unique' => 'Ce numéro de conteneur existe déjà.',
        ]);

        try {
            // Mise à jour du conteneur
            $conteneur->update($validatedData);

            return redirect()->route('conteneur.index')
                            ->with('success', 'Conteneur ' . $conteneur->name_conteneur . ' modifié avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Erreur lors de la modification du conteneur: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $conteneur = Conteneur::findOrFail($id);
            $conteneurName = $conteneur->name_conteneur;
            $conteneur->delete();

            return redirect()->route('conteneur.index')
                            ->with('success', 'Conteneur "' . $conteneurName . '" supprimé avec succès!');

        } catch (\Exception $e) {
            return redirect()->route('conteneur.index')
                            ->with('error', 'Erreur lors de la suppression du conteneur: ' . $e->getMessage());
        }
    }

    public function history(){
        $conteneurs = Conteneur::withCount('colis')->where('statut','fermer')->paginate(10);
        return view('admin.conteneur.history', compact('conteneurs'));
    }

    public function showColis(Request $request, $conteneurId)
    {
        try {
            $conteneur = Conteneur::with(['colis' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($conteneurId);

            return view('admin.conteneur.colis', compact('conteneur'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('conteneur.history')
                ->with('error', 'Conteneur non trouvé.');
        }
    }

    /**
     * Récupérer les détails d'un colis via AJAX
     */
    public function getColisDetails($id)
    {
        try {
            $colis = Colis::findOrFail($id);
            return response()->json($colis);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Colis non trouvé'], 404);
        }
    }

    public function close($id)
{
    $conteneur = Conteneur::findOrFail($id);
    $conteneur->statut = 'fermer';
    $conteneur->save();

    return response()->json([
        'success' => true,
        'message' => 'Conteneur fermé avec succès'
    ]);
}

public function open($id)
{
    $conteneur = Conteneur::findOrFail($id);
    $conteneur->statut = 'ouvert';
    $conteneur->save();

    return response()->json([
        'success' => true,
        'message' => 'Conteneur ouvert avec succès'
    ]);
}

 public function downloadConteneurPDF($conteneurId)
    {
        try {
            $conteneur = Conteneur::with(['colis' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($conteneurId);

            // Calculer les statistiques pour chaque colis et regrouper les produits
            foreach ($conteneur->colis as $colis) {
                // Décoder le JSON des colis
                $colisData = json_decode($colis->colis, true);
                
                // Regrouper les produits par nom avec la quantité totale
                $produitsGroupes = [];
                if (is_array($colisData)) {
                    foreach ($colisData as $item) {
                        $produit = $item['produit'];
                        $quantite = $item['quantite'];
                        
                        if (isset($produitsGroupes[$produit])) {
                            $produitsGroupes[$produit] += $quantite;
                        } else {
                            $produitsGroupes[$produit] = $quantite;
                        }
                    }
                }
                
                // Ajouter les produits groupés au colis
                $colis->produits_groupes = $produitsGroupes;
                
                // Calculer le nombre total de produits uniques
                $colis->nombre_produits_uniques = count($produitsGroupes);
                
                // Calculer la quantité totale
                $colis->quantite_totale = array_sum($produitsGroupes);
            }

            $pdf = PDF::loadView('admin.conteneur.pdf-template', compact('conteneur'));
            
            return $pdf->download("conteneur-{$conteneur->name_conteneur}-".now()->format('Y-m-d').".pdf");
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Conteneur non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF: '.$e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération du PDF'], 500);
        }
    }
}
