<?php

namespace App\Http\Controllers\Admin\Programme;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function type(){
        return view('admin.programme.type');
    }

    public function list(){
        return view('admin.programme.list');
    }

    public function search(Request $request)
    {
        $reference = $request->get('reference');
        
        $depot = Depot::where('reference', $reference)->first();
        
        if ($depot) {
            return response()->json([
                'success' => true,
                'depot' => [
                    'id' => $depot->id,
                    'reference' => $depot->reference,
                    'nature_objet' => $depot->nature_objet,
                    'quantite' => $depot->quantite,
                    'nom_concerne' => $depot->nom_concerne,
                    'prenom_concerne' => $depot->prenom_concerne,
                    'contact' => $depot->contact,
                    'email' => $depot->email,
                    'adresse_depot' => $depot->adresse_depot,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Dépôt non trouvé'
        ], 404);
    }

    public function getDetails($id)
    {
        $depot = Depot::find($id);
        
        if ($depot) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $depot->id,
                    'reference' => $depot->reference,
                    'nature_objet' => $depot->nature_objet,
                    'quantite' => $depot->quantite,
                    'nom_concerne' => $depot->nom_concerne,
                    'prenom_concerne' => $depot->prenom_concerne,
                    'contact' => $depot->contact,
                    'email' => $depot->email,
                    'adresse_depot' => $depot->adresse_depot,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Dépôt non trouvé'
        ], 404);
    }
}
