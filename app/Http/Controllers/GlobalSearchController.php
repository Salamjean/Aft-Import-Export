<?php

namespace App\Http\Controllers;

use App\Models\Colis;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function indexAdmin()
    {
        return view('admin.recherche.global');
    }

    public function indexAgent()
    {
        return view('agent.recherche.global');
    }

    public function indexIvoire()
    {
        return view('ivoire.recherche.global');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $results = Colis::with(['user', 'agenceExpedition', 'agenceDestination', 'conteneur', 'service'])
            ->where('reference_colis', 'LIKE', "%$query%")
            ->orWhere('name_expediteur', 'LIKE', "%$query%")
            ->orWhere('prenom_expediteur', 'LIKE', "%$query%")
            ->orWhere('name_destinataire', 'LIKE', "%$query%")
            ->orWhere('prenom_destinataire', 'LIKE', "%$query%")
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($results);
    }
}
