<?php

namespace App\Http\Controllers\Admin\Agence;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgenceController extends Controller
{
    public function create()
    {
        $agences = Agence::orderBy('created_at', 'desc')->paginate(5);
        return view('admin.agence.create', compact('agences'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:agences,name',
            'pays' => 'required|string|max:255',
            'devise' => 'required|string|max:10',
            'adresse' => 'required|string|max:500',
        ], [
            'name.required' => 'Le nom de l\'agence est obligatoire.',
            'name.unique' => 'Une agence avec ce nom existe déjà.',
            'pays.required' => 'Le pays est obligatoire.',
            'devise.required' => 'La devise est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
        ]);

        try {
            // Création de l'agence
            $agence = Agence::create([
                'name' => $validated['name'],
                'pays' => $validated['pays'],
                'devise' => $validated['devise'],
                'adresse' => $validated['adresse'],
            ]);

            // Redirection avec message de succès
            return redirect()
                ->route('agence.create')
                ->with('success', 'Agence créée avec succès!');

        } catch (\Exception $e) {
            // En cas d'erreur
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'agence: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            // Récupération de l'agence
            $agence = Agence::findOrFail($id);
            
            // Récupération de la liste des agences pour l'affichage
            $agences = Agence::where('id', '!=', $id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

            return view('admin.agence.create', compact('agence', 'agences'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('agence.create')
                ->with('error', 'Agence non trouvée.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('agence.create')
                ->with('error', 'Erreur lors du chargement de l\'agence: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:agences,name,' . $id,
            ],
            'pays' => [
                'required',
                'string',
                'max:255',
                'in:France,Côte d\'Ivoire,Chine'
            ],
            'devise' => [
                'required',
                'string',
                'max:10',
                'in:EUR,USD,CAD,CHF,XOF,GBP,JPY'
            ],
            'adresse' => [
                'required',
                'string',
                'max:500',
                'min:10'
            ]
        ], [
            'name.required' => 'Le nom de l\'agence est obligatoire.',
            'name.unique' => 'Une agence avec ce nom existe déjà.',
            'name.regex' => 'Le nom contient des caractères non autorisés.',
            'pays.required' => 'Le pays est obligatoire.',
            'pays.in' => 'Le pays sélectionné n\'est pas valide.',
            'devise.required' => 'La devise est obligatoire.',
            'devise.in' => 'La devise sélectionnée n\'est pas valide.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'adresse.min' => 'L\'adresse doit contenir au moins 10 caractères.',
        ]);

        DB::beginTransaction();

        try {
            // Récupération de l'agence
            $agence = Agence::findOrFail($id);
            
            // Sauvegarde de l'ancien nom pour le log
            $oldName = $agence->name;

            // Mise à jour de l'agence
            $agence->update([
                'name' => trim($validated['name']),
                'pays' => $validated['pays'],
                'devise' => $validated['devise'],
                'adresse' => trim($validated['adresse']),
            ]);

            DB::commit();

            // Journalisation
            Log::info('Agence mise à jour', [
                'agence_id' => $agence->id,
                'ancien_nom' => $oldName,
                'nouveau_nom' => $agence->name,
                'updated_by' => Auth::guard('admin')->id ?? 'system'
            ]);

            return redirect()
                ->route('agence.create')
                ->with([
                    'success' => 'Agence "'.$agence->name.'" mise à jour avec succès!',
                    'highlight_agence' => $agence->id
                ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()
                ->route('agence.create')
                ->with('error', 'Agence non trouvée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour agence', [
                'agence_id' => $id,
                'error' => $e->getMessage(),
                'input' => $request->except('_token', '_method')
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'agence. Veuillez réessayer.');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Récupération de l'agence
            $agence = Agence::findOrFail($id);
            $agenceName = $agence->name;

            // Vérification s'il y a des dépendances (optionnel)
            // if ($agence->users()->exists()) {
            //     return redirect()
            //         ->route('agence.create')
            //         ->with('error', 'Impossible de supprimer cette agence car elle contient des utilisateurs.');
            // }

            // Suppression de l'agence
            $agence->delete();

            DB::commit();

            // Journalisation
            Log::info('Agence supprimée', [
                'agence_id' => $id,
                'agence_name' => $agenceName,
                'deleted_by' => Auth::guard('admin')->id ?? 'system'
            ]);

            return redirect()
                ->route('agence.create')
                ->with('success', 'Agence "'.$agenceName.'" supprimée avec succès!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()
                ->route('agence.create')
                ->with('error', 'Agence non trouvée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression agence', [
                'agence_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('agence.create')
                ->with('error', 'Erreur lors de la suppression de l\'agence. Veuillez réessayer.');
        }
    }
}
