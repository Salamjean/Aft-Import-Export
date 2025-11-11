<?php

namespace App\Http\Controllers\Admin\Colis;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProduitController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designation' => 'required|string|max:255',
            'prix_unitaire' => 'required|numeric|min:0',
            'agence_destination_id' => 'required|exists:agences,id'
        ], [
            'designation.required' => 'La désignation du produit est obligatoire.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.numeric' => 'Le prix unitaire doit être un nombre.',
            'prix_unitaire.min' => 'Le prix unitaire doit être supérieur ou égal à 0.',
            'agence_destination_id.required' => 'L\'agence de destination est obligatoire.',
            'agence_destination_id.exists' => 'L\'agence sélectionnée n\'existe pas.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $produit = Produit::create([
                'designation' => $request->designation,
                'prix_unitaire' => $request->prix_unitaire,
                'agence_destination_id' => $request->agence_destination_id
            ]);

            // Charger la relation agenceDestination pour la réponse
            $produit->load('agenceDestination');

            return response()->json([
                'success' => true,
                'produit' => $produit,
                'message' => 'Produit ajouté avec succès!'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du produit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
{
    $searchTerm = $request->get('q');
    $agenceDestinationId = $request->get('agence_destination_id');

    $produits = Produit::where('designation', 'LIKE', "%{$searchTerm}%")
                      ->when($agenceDestinationId, function($query) use ($agenceDestinationId) {
                          return $query->where('agence_destination_id', $agenceDestinationId);
                      })
                      ->with('agenceDestination')
                      ->limit(10)
                      ->get();

    return response()->json($produits);
}

    public function index()
    {
        $produits = Produit::with('agenceDestination')->get();
        return response()->json($produits);
    }
}
