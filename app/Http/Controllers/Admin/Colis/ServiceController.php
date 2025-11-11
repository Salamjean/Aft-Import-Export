<?php

namespace App\Http\Controllers\Admin\Colis;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designation' => 'required|string|max:255',
            'prix_unitaire' => 'required|numeric|min:0',
            'agence_destination_id' => 'required|exists:agences,id',
            'description' => 'nullable|string',
            'type_service' => 'required|in:obligatoire,optionnel'
        ], [
            'designation.required' => 'La désignation du service est obligatoire.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.numeric' => 'Le prix unitaire doit être un nombre.',
            'prix_unitaire.min' => 'Le prix unitaire doit être supérieur ou égal à 0.',
            'agence_destination_id.required' => 'L\'agence de destination est obligatoire.',
            'agence_destination_id.exists' => 'L\'agence sélectionnée n\'existe pas.',
            'type_service.required' => 'Le type de service est obligatoire.',
            'type_service.in' => 'Le type de service doit être "obligatoire" ou "optionnel".'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $service = Service::create([
                'designation' => $request->designation,
                'prix_unitaire' => $request->prix_unitaire,
                'agence_destination_id' => $request->agence_destination_id,
                'description' => $request->description,
                'type_service' => $request->type_service
            ]);

            // Charger la relation agenceDestination pour la réponse
            $service->load('agenceDestination');

            return response()->json([
                'success' => true,
                'service' => $service,
                'message' => 'Service ajouté avec succès!'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $searchTerm = $request->get('q');
        $agenceDestinationId = $request->get('agence_destination_id');

        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json([], 200);
        }

        try {
            $services = Service::where('designation', 'LIKE', "%{$searchTerm}%")
                              ->when($agenceDestinationId, function($query) use ($agenceDestinationId) {
                                  return $query->where('agence_destination_id', $agenceDestinationId);
                              })
                              ->with('agenceDestination')
                              ->limit(10)
                              ->get();

            return response()->json($services);

        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function index()
    {
        $services = Service::with('agenceDestination')->get();
        return response()->json($services);
    }
}
