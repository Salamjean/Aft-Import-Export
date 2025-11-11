<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Agent;
use App\Models\Livreur;
use App\Models\NaissanceCertificat;
use App\Models\ResetCodePasswordAgent;
use App\Notifications\SendEmailToAgentAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{

    public function create()
    {
        $agences = Agence::all();
        $agents = Agent::with('agence')
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return view('admin.agent.create', compact('agences', 'agents'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'prenom' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'unique:agents,email'
            ],
            'contact' => [
                'required',
                'string',
                'max:20',
                'unique:agents,contact',
            ],
            'agence_id' => [
                'required',
                'exists:agences,id'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ]
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.regex' => 'Le nom contient des caractères non autorisés.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.regex' => 'Le prénom contient des caractères non autorisés.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact est obligatoire.',
            'contact.unique' => 'Ce numéro de contact est déjà utilisé.',
            'contact.regex' => 'Le format du contact n\'est pas valide.',
            'agence_id.required' => 'L\'agence est obligatoire.',
            'agence_id.exists' => 'L\'agence sélectionnée n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        DB::beginTransaction();

        try {
            // Création de l'agent
            $agent = Agent::create([
                'name' => trim($validated['name']),
                'prenom' => trim($validated['prenom']),
                'email' => strtolower(trim($validated['email'])),
                'contact' => trim($validated['contact']),
                'agence_id' => $validated['agence_id'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => $request->has('email_verified') ? now() : null,
            ]);

            DB::commit();

            // Journalisation
            Log::info('Nouvel agent créé', [
                'agent_id' => $agent->id,
                'name' => $agent->name,
                'email' => $agent->email,
                'agence_id' => $agent->agence_id,
                'created_by' => Auth::guard('admin')->id ?? 'system'
            ]);

            return redirect()
                ->route('agent.create')
                ->with([
                    'success' => 'Agent '.$agent->prenom.' '.$agent->name.' créé avec succès!',
                    'highlight_agent' => $agent->id
                ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur création agent', [
                'error' => $e->getMessage(),
                'input' => $request->except('password', 'password_confirmation')
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'agent. Veuillez réessayer.');
        }
    }

    public function edit($id)
    {
        try {
            $agent = Agent::findOrFail($id);
            $agences = Agence::all();

            return view('admin.agent.edit', compact('agent', 'agences'));

        } catch (\Exception $e) {
            return redirect()
                ->route('agent.create')
                ->with('error', 'Agent non trouvé.');
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
        ],
        'prenom' => [
            'required',
            'string',
            'max:255',
        ],
        'email' => [
            'required',
            'email',
            'unique:agents,email,' . $id
        ],
        'contact' => [
            'required',
            'string',
            'max:20',
            'unique:agents,contact,' . $id,
            'regex:/^[0-9+\-\s()]+$/'
        ],
        'agence_id' => [
            'required',
            'exists:agences,id'
        ]
    ], [
        'name.required' => 'Le nom est obligatoire.',
        'name.regex' => 'Le nom contient des caractères non autorisés.',
        'prenom.required' => 'Le prénom est obligatoire.',
        'prenom.regex' => 'Le prénom contient des caractères non autorisés.',
        'email.required' => 'L\'email est obligatoire.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'contact.required' => 'Le contact est obligatoire.',
        'contact.unique' => 'Ce numéro de contact est déjà utilisé.',
        'contact.regex' => 'Le format du contact n\'est pas valide.',
        'agence_id.required' => 'L\'agence est obligatoire.',
        'agence_id.exists' => 'L\'agence sélectionnée n\'existe pas.',
    ]);

    DB::beginTransaction();

    try {
        $agent = Agent::findOrFail($id);
        
        $agent->update([
            'name' => trim($validated['name']),
            'prenom' => trim($validated['prenom']),
            'email' => strtolower(trim($validated['email'])),
            'contact' => trim($validated['contact']),
            'agence_id' => $validated['agence_id'],
        ]);

        DB::commit();

        Log::info('Agent mis à jour', [
            'agent_id' => $agent->id,
            'name' => $agent->name,
            'email' => $agent->email,
            'agence_id' => $agent->agence_id,
            'updated_by' => Auth::guard('admin')->id ?? 'system'
        ]);

        // Si c'est une requête AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Agent "'.$agent->prenom.' '.$agent->name.'" mis à jour avec succès!'
            ]);
        }

        return redirect()
            ->route('agent.create')
            ->with([
                'success' => 'Agent "'.$agent->prenom.' '.$agent->name.'" mis à jour avec succès!',
                'highlight_agent' => $agent->id
            ]);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Erreur mise à jour agent', [
            'agent_id' => $id,
            'error' => $e->getMessage(),
            'input' => $request->all()
        ]);

        // Si c'est une requête AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'agent. Veuillez réessayer.'
            ], 500);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Erreur lors de la mise à jour de l\'agent. Veuillez réessayer.');
    }
}

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $agent = Agent::findOrFail($id);
            $agentName = $agent->prenom . ' ' . $agent->name;
            
            $agent->delete();

            DB::commit();

            Log::info('Agent supprimé', [
                'agent_id' => $id,
                'agent_name' => $agentName,
            ]);

            return redirect()
                ->route('agent.create')
                ->with('success', 'Agent "'.$agentName.'" supprimé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur suppression agent', [
                'agent_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('agent.create')
                ->with('error', 'Erreur lors de la suppression de l\'agent. Veuillez réessayer.');
        }
    }

}
