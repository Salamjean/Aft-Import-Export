<?php

namespace App\Http\Controllers\Agent\Cote_Ivoire;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Chauffeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IvoireChauffeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chauffeurs = Chauffeur::with('agence')
            ->orderBy('created_at', 'desc')
            ->paginate(1);
            
        $agences = Agence::get();

        return view('ivoire.chauffeur.index', compact('chauffeurs', 'agences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;
         $chauffeurs = Chauffeur::with('agence')
            ->where('agence_id', $agenceId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $agences = Agence::get();
            
        return view('ivoire.chauffeur.create', compact('agences','chauffeurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('chauffeurs')->whereNull('archived_at')
            ],
            'contact' => [
                'required',
                'string',
                'max:255',
                Rule::unique('chauffeurs')->whereNull('archived_at')
            ],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact est obligatoire.',
            'contact.unique' => 'Ce contact est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        try {
            DB::beginTransaction();

            $chauffeur = Chauffeur::create([
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'contact' => $validated['contact'],
                'password' => Hash::make($validated['password']),
                'agence_id' => $agenceId,
                'email_verified_at' => now(), // Optionnel : vérification immédiate
            ]);

            DB::commit();

            return redirect()->route('ivoire.chauffeur.create')
                ->with('success', 'Chauffeur créé avec succès!')
                ->with('highlight_chauffeur', $chauffeur->id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du chauffeur: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Chauffeur $chauffeur)
    {
        $chauffeur->load('agence');
        
        return view('ivoire.chauffeur.show', compact('chauffeur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chauffeur $chauffeur)
    {
        $agences = Agence::where('archived_at', null)
            ->orderBy('name')
            ->get();
            
        return view('ivoire.chauffeur.edit', compact('chauffeur', 'agences'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chauffeur $chauffeur)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('chauffeurs')
                    ->ignore($chauffeur->id)
                    ->whereNull('archived_at')
            ],
            'contact' => [
                'required',
                'string',
                'max:255',
                Rule::unique('chauffeurs')
                    ->ignore($chauffeur->id)
                    ->whereNull('archived_at')
            ],
            'agence_id' => 'required|exists:agences,id'
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact est obligatoire.',
            'contact.unique' => 'Ce contact est déjà utilisé.',
            'agence_id.required' => 'L\'agence est obligatoire.',
            'agence_id.exists' => 'L\'agence sélectionnée n\'existe pas.'
        ]);

        try {
            DB::beginTransaction();

            $chauffeur->update([
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'contact' => $validated['contact'],
                'agence_id' => $validated['agence_id'],
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chauffeur mis à jour avec succès!'
                ]);
            }

            return redirect()->route('ivoire.chauffeur.create')
                ->with('success', 'Chauffeur mis à jour avec succès!')
                ->with('highlight_chauffeur', $chauffeur->id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du chauffeur: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chauffeur $chauffeur)
    {
        try {
            DB::beginTransaction();

            // Vérifier s'il y a des dépendances avant suppression
            // Exemple: if ($chauffeur->trajets()->exists()) { ... }
            
            $chauffeur->delete();

            DB::commit();

            return redirect()->route('ivoire.chauffeur.create')
                ->with('success', 'Chauffeur supprimé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du chauffeur: ' . $e->getMessage());
        }
    }

    /**
     * Archive the specified resource.
     */
    public function archive(Chauffeur $chauffeur)
    {
        try {
            DB::beginTransaction();

            $chauffeur->update([
                'archived_at' => now()
            ]);

            DB::commit();

            return redirect()->route('ivoire.chauffeur.create')
                ->with('success', 'Chauffeur archivé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'archivage du chauffeur: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $chauffeur = Chauffeur::withTrashed()->findOrFail($id);
            $chauffeur->update([
                'archived_at' => null
            ]);

            DB::commit();

            return redirect()->route('ivoire.chauffeur.create')
                ->with('success', 'Chauffeur restauré avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la restauration du chauffeur: ' . $e->getMessage());
        }
    }

    /**
     * Show archived chauffeurs.
     */
    public function archived()
    {
        $chauffeurs = Chauffeur::whereNotNull('archived_at')
            ->with('agence')
            ->orderBy('archived_at', 'desc')
            ->paginate(10);

        return view('ivoire.chauffeur.archived', compact('chauffeurs'));
    }

    /**
     * Update password for chauffeur.
     */
    public function updatePassword(Request $request, Chauffeur $chauffeur)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        try {
            DB::beginTransaction();

            $chauffeur->update([
                'password' => Hash::make($validated['password'])
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Mot de passe mis à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du mot de passe: ' . $e->getMessage());
        }
    }
}
