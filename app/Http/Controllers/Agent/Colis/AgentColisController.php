<?php

namespace App\Http\Controllers\Agent\Colis;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Colis;
use App\Models\Conteneur;
use App\Models\Produit;
use App\Models\Paiement;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Str;

class AgentColisController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();

        if (!$agent || !$agent->agence_id) {
            $colis = collect()->paginate(10);
            return view('agent.colis.index', compact('colis'));
        }

        // Filtrer uniquement les colis de l'agence de l'agent connecté
        $query = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
            ->where('statut', 'valide')
            ->where('agence_expedition_id', $agent->agence_id) // Utiliser l'agence de l'agent
            ->orderBy('created_at', 'desc');

        // Filtre de recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_colis', 'LIKE', "%{$search}%")
                    ->orWhere('name_expediteur', 'LIKE', "%{$search}%")
                    ->orWhere('name_destinataire', 'LIKE', "%{$search}%")
                    ->orWhere('email_expediteur', 'LIKE', "%{$search}%")
                    ->orWhere('email_destinataire', 'LIKE', "%{$search}%")
                    ->orWhere('code_colis', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->has('status') && !empty($request->status)) {
            $query->where('statut', $request->status);
        }

        // Filtre par mode de transit
        if ($request->has('mode_transit') && !empty($request->mode_transit)) {
            $query->where('mode_transit', $request->mode_transit);
        }

        // Filtre par statut de paiement
        if ($request->has('paiement') && !empty($request->paiement)) {
            $query->where('statut_paiement', $request->paiement);
        }

        $colis = $query->paginate(10);

        // Compter le nombre de tableaux (types de colis) dans le champ colis (JSON)
        $colis->getCollection()->transform(function ($item) {
            $colisData = json_decode($item->colis, true);
            $item->nombre_types_colis = is_array($colisData) ? count($colisData) : 0;
            return $item;
        });

        return view('agent.colis.index', compact('colis'));
    }

    public function create()
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();

        if (!$agent || !$agent->agence_id) {
            return redirect()->back()
                ->with('error', 'Agent non connecté ou aucune agence associée');
        }

        // Récupérer l'agence de l'agent connecté
        $agenceExpedition = Agence::find($agent->agence_id);

        if (!$agenceExpedition) {
            return redirect()->back()
                ->with('error', 'Agence non trouvée pour cet agent');
        }

        // Par défaut, on commence avec le mode Maritime
        $modeTransit = 'Maritime';
        $typeConteneur = 'Conteneur';

        // Récupérer un conteneur ouvert pour CETTE AGENCE spécifique
        $conteneur = Conteneur::where('statut', 'ouvert')
            ->where('type_conteneur', $typeConteneur)
            ->where('agence_id', $agenceExpedition->id) // FILTRE PAR AGENCE
            ->orderBy('created_at', 'asc')
            ->first();

        // Si aucun conteneur ouvert pour cette agence, créer un nouveau
        if (!$conteneur) {
            $conteneur = new Conteneur();
            $conteneur->name_conteneur = $typeConteneur . ' - ' . $agenceExpedition->name . ' - ' . date('Y-m-d');
            $conteneur->type_conteneur = $typeConteneur;
            $conteneur->statut = 'ouvert';
            $conteneur->agence_id = $agenceExpedition->id; // ASSOCIER LE CONTENEUR À L'AGENCE
            $conteneur->save();
        }

        // Récupérer les services
        $services = Service::all();

        // Récupérer les produits
        $produits = Produit::with('agenceDestination')->get();

        // Générer la référence automatique selon les nouvelles règles
        $initiales = $this->genererInitiales($agent->name, $agent->prenom);

        // Référence par défaut pour l'affichage initial
        $reference = $this->genererReference($initiales, $modeTransit, $agenceExpedition->id);

        return view('agent.colis.create', compact(
            'conteneur',
            'agenceExpedition',
            'reference',
            'services',
            'produits',
            'modeTransit'
        ));
    }

    // Nouvelle méthode pour générer les initiales
    private function genererInitiales($nom, $prenom = null)
    {
        $initiales = '';

        // Première lettre du nom
        if (!empty($nom)) {
            $initiales .= strtoupper(substr($nom, 0, 1));
        }

        // Première lettre du prénom
        if (!empty($prenom)) {
            $initiales .= strtoupper(substr($prenom, 0, 1));
        }

        // Si pas de prénom, prendre la deuxième lettre du nom
        if (empty($prenom) && strlen($nom) >= 2) {
            $initiales .= strtoupper(substr($nom, 1, 1));
        }

        return $initiales;
    }

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

            // Récupérer le conteneur du colis
            $conteneur = $colis->conteneur;

            // Si le conteneur n'existe plus, créer un nouveau conteneur par défaut
            if (!$conteneur) {
                $typeConteneur = 'Conteneur';
                $conteneur = Conteneur::where('statut', 'ouvert')
                    ->where('type_conteneur', $typeConteneur)
                    ->orderBy('created_at', 'asc')
                    ->first();

                if (!$conteneur) {
                    $conteneur = new Conteneur();
                    $conteneur->name_conteneur = $typeConteneur . ' ' . date('Y-m-d');
                    $conteneur->type_conteneur = $typeConteneur;
                    $conteneur->statut = 'ouvert';
                    $conteneur->save();
                }
            }

            // Récupérer les agences d'expédition (pays != Côte d'Ivoire)
            $agencesExpedition = Agence::where('pays', '!=', 'Côte d\'Ivoire')->get();

            // Récupérer les services
            $services = Service::all();

            // Récupérer les produits
            $produits = Produit::with('agenceDestination')->get();

            // Récupérer le mode de transit actuel
            $modeTransit = $colis->mode_transit;

            // Récupérer l'utilisateur connecté pour les initiales
            $user = Auth::guard('agent')->user();
            $initiales = strtoupper(substr($user->name, 0, 2));

            // Générer la référence (même logique que create mais avec les données existantes)
            $reference = $this->genererReference(
                $initiales,
                $modeTransit,
                $colis->agence_expedition_id
            );

            // Décoder les détails des colis (déjà casté en array)
            $colisDetails = $colis->colis ?? [];

            return view('agent.colis.edit', compact(
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
            Log::error('Erreur lors de la récupération du colis pour édition: ' . $e->getMessage());

            return redirect()->route('colis.index')
                ->with('error', 'Colis non trouvé ou erreur lors du chargement: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            // Transport
            'conteneur_id' => 'required|exists:conteneurs,id',
            'reference_colis' => 'required|string',
            'mode_transit' => 'required|in:Maritime,Aerien',
            'agence_expedition_id' => 'required|exists:agences,id',
            'agence_destination_id' => 'required|exists:agences,id',
            'devise' => 'required|string',

            // Expéditeur
            'user_id' => 'nullable|exists:users,id',
            'type_expediteur' => 'required|in:particulier,societe',
            'name_expediteur' => 'required|string|max:255',
            'prenom_expediteur' => 'nullable|string|max:255',
            'email_expediteur' => 'nullable|email',
            'contact_expediteur' => 'required|string|max:255',
            'adresse_expediteur' => 'required|string',

            // Destinataire
            'name_destinataire' => 'required|string|max:255',
            'prenom_destinataire' => 'required|string|max:255',
            'email_destinataire' => 'nullable|email',
            'indicatif' => 'required|string',
            'contact_destinataire' => 'required|string|max:255',
            'adresse_destinataire' => 'required|string',

            // Colis
            'colis' => 'required|array',
            'colis.*.quantite' => 'required|integer|min:1',
            'colis.*.produit' => 'required|string',
            'colis.*.prix_unitaire' => 'required|numeric|min:0',
            'colis.*.longueur' => 'nullable|numeric|min:0',
            'colis.*.largeur' => 'nullable|numeric|min:0',
            'colis.*.hauteur' => 'nullable|numeric|min:0',
            'colis.*.poids' => 'nullable|numeric|min:0',
            'colis.*.description' => 'nullable|string',
            'colis.*.type_colis' => 'required|in:Standard,Fragile',

            // Services
            'service_id' => 'nullable|exists:services,id',
            'prix_service' => 'nullable|numeric|min:0',

            // Paiement
            'methode_paiement' => 'required|in:espece,virement_bancaire,cheque,mobile_money,livraison',
            'nom_banque' => 'required_if:methode_paiement,virement_bancaire|nullable|string|max:255',
            'numero_compte' => 'required_if:methode_paiement,virement_bancaire|nullable|string|max:255',
            'operateur_mobile_money' => 'required_if:methode_paiement,mobile_money|nullable|in:WAVE,ORANGE,MOOV,MTN',
            'numero_mobile_money' => 'required_if:methode_paiement,mobile_money|nullable|string|max:255',
            'montant_paye' => 'required|numeric|min:0',
            'reste_a_payer' => 'nullable|numeric|min:0',
            'statut_paiement' => 'required|in:non_paye,partiellement_paye,totalement_paye',
            'notes_paiement' => 'nullable|string',
        ]);

        try {
            // Vérifier si la référence existe déjà ou si l'incrément n'est pas disponible
            $referenceInitiale = $request->reference_colis;
            $referenceFinale = $this->verifierEtGenererReference($referenceInitiale, $request->mode_transit, $request->agence_expedition_id);

            $referenceModifiee = false;
            if ($referenceFinale !== $referenceInitiale) {
                $referenceModifiee = true;
            }

            // Calcul des montants
            $montantColis = $this->calculerMontantColis($request->colis, $request->mode_transit);
            $montantTotal = $montantColis + ($request->prix_service ?? 0);

            // Calculer la quantité totale
            $quantiteTotale = $this->calculerQuantiteTotale($request->colis);

            // Générer le code_colis principal
            $codeColisPrincipal = $this->genererCodeColis($quantiteTotale, $referenceFinale);

            // Récupérer les noms des agences
            $agenceExpedition = Agence::find($request->agence_expedition_id);
            $agenceDestination = Agence::find($request->agence_destination_id);

            // Créer un tableau pour stocker tous les codes_colis et leurs statuts
            $codesColis = [
                'principal' => $codeColisPrincipal,
                'individuels' => []
            ];

            // Créer le tableau des statuts individuels
            $statutsIndividuels = [];

            // Remplir les tableaux codesColis et statutsIndividuels
            $compteurUnite = 1;
            foreach ($request->colis as $index => $item) {
                for ($unite = 1; $unite <= $item['quantite']; $unite++) {
                    // Générer un code colis unique pour chaque unité
                    $codeColisIndividuel = $this->genererCodeColisUnique();

                    // Ajouter au tableau des codes individuels
                    $codesColis['individuels'][] = [
                        'code_colis' => $codeColisIndividuel,
                        'colis_numero' => $index + 1,
                        'unite_numero' => $unite,
                        'produit' => $item['produit'],
                        'description' => $item['description'] ?? null,
                        'poids' => $item['poids'] ?? null,
                        'dimensions' => [
                            'longueur' => $item['longueur'] ?? null,
                            'largeur' => $item['largeur'] ?? null,
                            'hauteur' => $item['hauteur'] ?? null,
                        ]
                    ];

                    // Créer le statut individuel pour cette unité
                    $statutsIndividuels[$codeColisIndividuel] = [
                        'code_colis' => $codeColisIndividuel,
                        'colis_numero' => $index + 1,
                        'unite_numero' => $unite,
                        'produit' => $item['produit'],
                        'statut' => 'valide', // Statut initial
                        'date_creation' => now()->toDateTimeString(),
                        'date_modification' => now()->toDateTimeString(),
                        'localisation_actuelle' => $agenceExpedition->name,
                        'agence_actuelle_id' => $agenceExpedition->id,
                        'notes' => 'Colis créé et en attente de traitement',
                        'historique' => [
                            [
                                'statut' => 'valide',
                                'date' => now()->toDateTimeString(),
                                'localisation' => $agenceExpedition->name,
                                'agence_id' => $agenceExpedition->id,
                                'notes' => 'Colis créé avec statut initial'
                            ]
                        ]
                    ];

                    $compteurUnite++;
                }
            }

            // MAINTENANT générer les QR codes avec les VRAIS codes_colis INDIVIDUELS
            $qrCodesData = $this->genererEtSauvegarderQRCode(
                $request->colis,
                $referenceFinale,
                $codeColisPrincipal,
                [
                    'name_expediteur' => $request->name_expediteur,
                    'name_destinataire' => $request->name_destinataire,
                    'montant_total' => $montantTotal,
                    'devise' => $request->devise,
                    'quantite_totale' => $quantiteTotale,
                    'agence_expedition' => $agenceExpedition->name,
                    'agence_destination' => $agenceDestination->name
                ],
                $codesColis['individuels'] // Passer les codes individuels
            );

            // Création du colis
            $colis = Colis::create([
                'conteneur_id' => $request->conteneur_id,
                'reference_colis' => $referenceFinale,
                'code_colis' => json_encode($codesColis),
                'mode_transit' => $request->mode_transit,
                'agence_expedition_id' => $request->agence_expedition_id,
                'agence_destination_id' => $request->agence_destination_id,
                'agence_destination' => $agenceDestination->name,
                'agence_expedition' => $agenceExpedition->name,
                'devise' => $request->devise,

                // Expéditeur
                'user_id' => $request->user_id,
                'name_expediteur' => $request->name_expediteur,
                'prenom_expediteur' => $request->prenom_expediteur,
                'email_expediteur' => $request->email_expediteur,
                'contact_expediteur' => $request->contact_expediteur,
                'adresse_expediteur' => $request->adresse_expediteur,

                // Destinataire
                'name_destinataire' => $request->name_destinataire,
                'prenom_destinataire' => $request->prenom_destinataire,
                'email_destinataire' => $request->email_destinataire,
                'indicatif' => $request->indicatif,
                'contact_destinataire' => $request->contact_destinataire,
                'adresse_destinataire' => $request->adresse_destinataire,

                // Colis
                'colis' => json_encode($request->colis),
                'montant_colis' => $montantColis,
                'montant_paye_colis' => $request->montant_paye,
                'statut' => 'valide',

                // Services
                'service_id' => $request->service_id,
                'prix_service' => $request->prix_service,
                'montant_total' => $montantTotal,

                // Paiement
                'methode_paiement' => $request->methode_paiement,
                'nom_banque' => $request->nom_banque,
                'numero_compte' => $request->numero_compte,
                'operateur_mobile_money' => $request->operateur_mobile_money,
                'numero_mobile_money' => $request->numero_mobile_money,
                'montant_paye' => $request->montant_paye,
                'reste_a_payer' => $request->reste_a_payer,
                'statut_paiement' => $request->statut_paiement,
                'notes_paiement' => $request->notes_paiement,

                // QR Codes avec chemins des fichiers
                'qr_codes' => json_encode($qrCodesData),

                // NOUVEAU: Statuts individuels
                'statuts_individuels' => json_encode($statutsIndividuels),
            ]);

            // Envoi d'email uniquement à l'expéditeur s'il a un email
            if ($colis->email_expediteur) {
                $this->sendNotificationToExpediteur($colis);
            }

            // Message de succès personnalisé selon si la référence a été modifiée
            $messageSucces = 'Colis créé avec succès. Code principal: ' . $codeColisPrincipal .
                '. ' . count($codesColis['individuels']) . ' codes individuels générés avec leurs statuts.';

            if ($referenceModifiee) {
                $messageSucces .= ' La référence a été automatiquement ajustée de "' . $referenceInitiale . '" à "' . $referenceFinale . '" car la référence initiale existait déjà ou n\'était pas disponible.';
            }

            if ($colis->email_expediteur) {
                $messageSucces .= ' Email envoyé à l\'expéditeur.';
            }

            return redirect()->route('agent.colis.index')
                ->with('success', $messageSucces);

        } catch (\Exception $e) {
            Log::error('Erreur création colis: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Erreur lors de la création du colis: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Vérifie si la référence existe déjà et génère la référence suivante si nécessaire
     */
    private function verifierEtGenererReference($referenceInitiale, $modeTransit, $agenceExpeditionId)
    {
        $reference = $referenceInitiale;

        // Extraire les parties de la référence
        $parts = explode('-', $reference);

        if (count($parts) >= 3) {
            $initiales = $parts[0];
            $incrementActuel = intval($parts[1]);
            $suffixe = $parts[2];

            // Vérifier si la référence exacte existe déjà
            $referenceExiste = Colis::where('reference_colis', $reference)->exists();

            // Vérifier si l'incrément est déjà utilisé (indépendamment des initiales)
            $incrementUtilise = $this->incrementEstUtilise($incrementActuel, $suffixe, $modeTransit, $agenceExpeditionId);

            // Si la référence existe OU si l'incrément est déjà utilisé, générer une nouvelle référence
            if ($referenceExiste || $incrementUtilise) {
                // Trouver le prochain incrément disponible (toujours prendre le suivant du maximum)
                $prochainIncrement = $this->trouverProchainIncrementDisponible($suffixe, $modeTransit, $agenceExpeditionId);

                // Reconstruire la référence avec le nouvel incrément
                $reference = $initiales . '-' . str_pad($prochainIncrement, 4, '0', STR_PAD_LEFT) . '-' . $suffixe;

                $raison = $referenceExiste ? 'existe déjà' : 'incrément déjà utilisé';
                Log::info("Référence ajustée ({$raison}): {$referenceInitiale} -> {$reference}");
            }
        }

        return $reference;
    }

    /**
     * Vérifie si un incrément spécifique est déjà utilisé pour un suffixe donné
     */
    private function incrementEstUtilise($increment, $suffixe, $modeTransit, $agenceExpeditionId)
    {
        // Chercher toutes les références avec le même suffixe et mode de transit
        $query = Colis::where('reference_colis', 'LIKE', '%-' . $suffixe)
            ->where('mode_transit', $modeTransit);

        // Filtrer par agence d'expédition si fournie
        if ($agenceExpeditionId) {
            $query->where('agence_expedition_id', $agenceExpeditionId);
        }

        $referencesExistantes = $query->pluck('reference_colis')->toArray();

        // Vérifier si l'incrément est présent dans les références existantes
        foreach ($referencesExistantes as $ref) {
            $parts = explode('-', $ref);
            if (count($parts) >= 2) {
                $incrementExistant = intval($parts[1]);
                if ($incrementExistant === $increment) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Trouve le prochain incrément disponible pour la référence
     * Toujours retourne le maximum existant + 1
     */
    private function trouverProchainIncrementDisponible($suffixe, $modeTransit, $agenceExpeditionId)
    {
        // Chercher toutes les références existantes avec le même suffixe et mode de transit
        $query = Colis::where('reference_colis', 'LIKE', '%-' . $suffixe)
            ->where('mode_transit', $modeTransit);

        // Filtrer par agence d'expédition si fournie
        if ($agenceExpeditionId) {
            $query->where('agence_expedition_id', $agenceExpeditionId);
        }

        $referencesExistantes = $query->pluck('reference_colis')->toArray();

        // Extraire tous les incréments existants
        $increments = [];
        foreach ($referencesExistantes as $ref) {
            $parts = explode('-', $ref);
            if (count($parts) >= 2) {
                $increments[] = intval($parts[1]);
            }
        }

        // Si aucun incrément trouvé, commencer à 1
        if (empty($increments)) {
            return 1;
        }

        // Retourner le maximum + 1
        return max($increments) + 1;
    }

    public function update(Request $request, $id)
    {
        // Validation des données (identique à store)
        $validated = $request->validate([
            // Transport
            'conteneur_id' => 'required|exists:conteneurs,id',
            'reference_colis' => 'required|string',
            'mode_transit' => 'required|in:Maritime,Aerien',
            'agence_expedition_id' => 'required|exists:agences,id',
            'agence_destination_id' => 'required|exists:agences,id',
            'devise' => 'required|string',

            // Expéditeur
            'user_id' => 'nullable|exists:users,id',
            'type_expediteur' => 'required|in:particulier,societe',
            'name_expediteur' => 'required|string|max:255',
            'prenom_expediteur' => 'nullable|string|max:255',
            'email_expediteur' => 'nullable|email',
            'contact_expediteur' => 'required|string|max:255',
            'adresse_expediteur' => 'required|string',

            // Destinataire
            'name_destinataire' => 'required|string|max:255',
            'prenom_destinataire' => 'required|string|max:255',
            'email_destinataire' => 'nullable|email',
            'indicatif' => 'required|string',
            'contact_destinataire' => 'required|string|max:255',
            'adresse_destinataire' => 'required|string',

            // Colis
            'colis' => 'required|array',
            'colis.*.quantite' => 'required|integer|min:1',
            'colis.*.produit' => 'required|string',
            'colis.*.prix_unitaire' => 'required|numeric|min:0',
            'colis.*.longueur' => 'nullable|numeric|min:0',
            'colis.*.largeur' => 'nullable|numeric|min:0',
            'colis.*.hauteur' => 'nullable|numeric|min:0',
            'colis.*.poids' => 'nullable|numeric|min:0',
            'colis.*.description' => 'nullable|string',
            'colis.*.type_colis' => 'required|in:Standard,Fragile',

            // Services
            'service_id' => 'nullable|exists:services,id',
            'prix_service' => 'nullable|numeric|min:0',

            // Paiement
            'methode_paiement' => 'required|in:espece,virement_bancaire,cheque,mobile_money,livraison',
            'nom_banque' => 'required_if:methode_paiement,virement_bancaire|nullable|string|max:255',
            'numero_compte' => 'required_if:methode_paiement,virement_bancaire|nullable|string|max:255',
            'operateur_mobile_money' => 'required_if:methode_paiement,mobile_money|nullable|in:WAVE,ORANGE,MOOV,MTN',
            'numero_mobile_money' => 'required_if:methode_paiement,mobile_money|nullable|string|max:255',
            'montant_paye' => 'required|numeric|min:0',
            'reste_a_payer' => 'nullable|numeric|min:0',
            'statut_paiement' => 'required|in:non_paye,partiellement_paye,totalement_paye',
            'notes_paiement' => 'nullable|string',
        ]);

        try {
            // Récupérer le colis existant
            $colis = Colis::findOrFail($id);

            // Calcul des montants
            $montantColis = $this->calculerMontantColis($request->colis, $request->mode_transit);
            $montantTotal = $montantColis + ($request->prix_service ?? 0);

            // Calculer la quantité totale
            $quantiteTotale = $this->calculerQuantiteTotale($request->colis);

            // Récupérer les noms des agences
            $agenceExpedition = Agence::find($request->agence_expedition_id);
            $agenceDestination = Agence::find($request->agence_destination_id);

            // Générer le code_colis principal
            $codeColisPrincipal = $this->genererCodeColis($quantiteTotale, $colis->reference_colis);

            // Récupérer les codes colis existants pour conserver les statuts
            $anciensCodesColis = json_decode($colis->code_colis, true) ?? [];
            $anciensStatutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];

            // Créer un tableau pour stocker tous les codes_colis et leurs statuts
            $codesColis = [
                'principal' => $codeColisPrincipal,
                'individuels' => []
            ];

            // Remplir les tableaux codesColis et statutsIndividuels
            $statutsIndividuels = [];
            $compteurUnite = 1;

            foreach ($request->colis as $index => $item) {
                for ($unite = 1; $unite <= $item['quantite']; $unite++) {
                    // Vérifier si on peut réutiliser un ancien code colis
                    $ancienCodeColis = $this->trouverAncienCodeColis($anciensCodesColis, $index, $unite);

                    if ($ancienCodeColis && isset($anciensStatutsIndividuels[$ancienCodeColis])) {
                        // Réutiliser l'ancien code colis et son statut
                        $codeColisIndividuel = $ancienCodeColis;

                        // Mettre à jour le statut existant avec les nouvelles informations
                        $statutsIndividuels[$codeColisIndividuel] = array_merge(
                            $anciensStatutsIndividuels[$codeColisIndividuel],
                            [
                                'colis_numero' => $index + 1,
                                'unite_numero' => $unite,
                                'produit' => $item['produit'],
                                'date_modification' => now()->toDateTimeString(),
                                'localisation_actuelle' => $agenceExpedition->name,
                                'agence_actuelle_id' => $agenceExpedition->id,
                                'notes' => 'Colis modifié - ' . ($anciensStatutsIndividuels[$codeColisIndividuel]['notes'] ?? 'Colis mis à jour')
                            ]
                        );

                        // Ajouter une entrée dans l'historique
                        $statutsIndividuels[$codeColisIndividuel]['historique'][] = [
                            'statut' => $statutsIndividuels[$codeColisIndividuel]['statut'],
                            'date' => now()->toDateTimeString(),
                            'localisation' => $agenceExpedition->name,
                            'agence_id' => $agenceExpedition->id,
                            'notes' => 'Colis modifié avec nouvelles informations'
                        ];
                    } else {
                        // Générer un nouveau code colis unique
                        $codeColisIndividuel = $this->genererCodeColisUnique();

                        // Créer un nouveau statut individuel
                        $statutsIndividuels[$codeColisIndividuel] = [
                            'code_colis' => $codeColisIndividuel,
                            'colis_numero' => $index + 1,
                            'unite_numero' => $unite,
                            'produit' => $item['produit'],
                            'statut' => 'valide',
                            'date_creation' => now()->toDateTimeString(),
                            'date_modification' => now()->toDateTimeString(),
                            'localisation_actuelle' => $agenceExpedition->name,
                            'agence_actuelle_id' => $agenceExpedition->id,
                            'notes' => 'Nouveau colis ajouté lors de la modification',
                            'historique' => [
                                [
                                    'statut' => 'valide',
                                    'date' => now()->toDateTimeString(),
                                    'localisation' => $agenceExpedition->name,
                                    'agence_id' => $agenceExpedition->id,
                                    'notes' => 'Colis créé lors de la modification'
                                ]
                            ]
                        ];
                    }

                    // Ajouter au tableau des codes individuels
                    $codesColis['individuels'][] = [
                        'code_colis' => $codeColisIndividuel,
                        'colis_numero' => $index + 1,
                        'unite_numero' => $unite,
                        'produit' => $item['produit'],
                        'description' => $item['description'] ?? null,
                        'poids' => $item['poids'] ?? null,
                        'dimensions' => [
                            'longueur' => $item['longueur'] ?? null,
                            'largeur' => $item['largeur'] ?? null,
                            'hauteur' => $item['hauteur'] ?? null,
                        ]
                    ];

                    $compteurUnite++;
                }
            }

            // Générer les nouveaux QR codes
            $qrCodesData = $this->genererEtSauvegarderQRCode(
                $request->colis,
                $colis->reference_colis,
                $codeColisPrincipal,
                [
                    'name_expediteur' => $request->name_expediteur,
                    'name_destinataire' => $request->name_destinataire,
                    'montant_total' => $montantTotal,
                    'devise' => $request->devise,
                    'quantite_totale' => $quantiteTotale,
                    'agence_expedition' => $agenceExpedition->name,
                    'agence_destination' => $agenceDestination->name
                ],
                $codesColis['individuels']
            );

            // Mettre à jour le colis existant
            $colis->update([
                'conteneur_id' => $request->conteneur_id,
                'reference_colis' => $colis->reference_colis, // Garder la référence originale
                'code_colis' => json_encode($codesColis),
                'mode_transit' => $request->mode_transit,
                'agence_expedition_id' => $request->agence_expedition_id,
                'agence_destination_id' => $request->agence_destination_id,
                'agence_destination' => $agenceDestination->name,
                'agence_expedition' => $agenceExpedition->name,
                'devise' => $request->devise,

                // Expéditeur
                'user_id' => $request->user_id,
                'name_expediteur' => $request->name_expediteur,
                'prenom_expediteur' => $request->prenom_expediteur,
                'email_expediteur' => $request->email_expediteur,
                'contact_expediteur' => $request->contact_expediteur,
                'adresse_expediteur' => $request->adresse_expediteur,

                // Destinataire
                'name_destinataire' => $request->name_destinataire,
                'prenom_destinataire' => $request->prenom_destinataire,
                'email_destinataire' => $request->email_destinataire,
                'indicatif' => $request->indicatif,
                'contact_destinataire' => $request->contact_destinataire,
                'adresse_destinataire' => $request->adresse_destinataire,

                // Colis
                'colis' => json_encode($request->colis),
                'montant_colis' => $montantColis,
                'montant_paye_colis' => $request->montant_paye,
                'statut' => 'valide',

                // Services
                'service_id' => $request->service_id,
                'prix_service' => $request->prix_service,
                'montant_total' => $montantTotal,

                // Paiement
                'methode_paiement' => $request->methode_paiement,
                'nom_banque' => $request->nom_banque,
                'numero_compte' => $request->numero_compte,
                'operateur_mobile_money' => $request->operateur_mobile_money,
                'numero_mobile_money' => $request->numero_mobile_money,
                'montant_paye' => $request->montant_paye,
                'reste_a_payer' => $request->reste_a_payer,
                'statut_paiement' => $request->statut_paiement,
                'notes_paiement' => $request->notes_paiement,

                // QR Codes avec chemins des fichiers
                'qr_codes' => json_encode($qrCodesData),

                // Statuts individuels mis à jour
                'statuts_individuels' => json_encode($statutsIndividuels),
            ]);

            // Compter les nouveaux codes colis générés
            $nouveauxCodes = array_filter($codesColis['individuels'], function ($code) use ($anciensCodesColis) {
                return !$this->codeExisteDansAnciens($code['code_colis'], $anciensCodesColis);
            });

            $messageSucces = 'Colis modifié avec succès. Référence: ' . $colis->reference_colis;

            if (count($nouveauxCodes) > 0) {
                $messageSucces .= '. ' . count($nouveauxCodes) . ' nouveaux codes colis générés avec leurs QR codes.';
            }

            return redirect()->route('agent.colis.index')
                ->with('success', $messageSucces);

        } catch (\Exception $e) {
            Log::error('Erreur modification colis: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du colis: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Méthode pour trouver un ancien code colis
    private function trouverAncienCodeColis($anciensCodesColis, $nouvelIndex, $nouvelleUnite)
    {
        if (!isset($anciensCodesColis['individuels']) || !is_array($anciensCodesColis['individuels'])) {
            return null;
        }

        foreach ($anciensCodesColis['individuels'] as $ancienCode) {
            if (
                isset($ancienCode['colis_numero']) && $ancienCode['colis_numero'] == $nouvelIndex + 1 &&
                isset($ancienCode['unite_numero']) && $ancienCode['unite_numero'] == $nouvelleUnite
            ) {
                return $ancienCode['code_colis'];
            }
        }

        return null;
    }

    // Méthode pour vérifier si un code existe dans les anciens codes
    private function codeExisteDansAnciens($codeColis, $anciensCodesColis)
    {
        if (!isset($anciensCodesColis['individuels']) || !is_array($anciensCodesColis['individuels'])) {
            return false;
        }

        foreach ($anciensCodesColis['individuels'] as $ancienCode) {
            if (isset($ancienCode['code_colis']) && $ancienCode['code_colis'] === $codeColis) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mettre à jour les statuts individuels lorsque les colis changent
     */
    private function mettreAJourStatutsIndividuels($colis, $nouveauxColis)
    {
        $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];
        $codesColis = json_decode($colis->code_colis, true) ?? ['individuels' => []];

        // Calculer la nouvelle quantité totale
        $nouvelleQuantiteTotale = 0;
        foreach ($nouveauxColis as $item) {
            $nouvelleQuantiteTotale += $item['quantite'];
        }

        // Quantité actuelle
        $quantiteActuelle = count($statutsIndividuels);

        if ($nouvelleQuantiteTotale > $quantiteActuelle) {
            // Ajouter de nouveaux statuts individuels
            $this->ajouterNouveauxStatuts($colis, $statutsIndividuels, $codesColis, $nouveauxColis, $quantiteActuelle);
        } elseif ($nouvelleQuantiteTotale < $quantiteActuelle) {
            // Supprimer des statuts individuels
            $this->supprimerStatuts($statutsIndividuels, $codesColis, $quantiteActuelle - $nouvelleQuantiteTotale);
        }

        // Mettre à jour les données dans la base
        $colis->statuts_individuels = json_encode($statutsIndividuels);
        $colis->code_colis = json_encode($codesColis);
    }

    /**
     * Ajouter de nouveaux statuts individuels
     */
    private function ajouterNouveauxStatuts($colis, &$statutsIndividuels, &$codesColis, $nouveauxColis, $quantiteActuelle)
    {
        $compteurUnite = $quantiteActuelle + 1;
        $agenceExpedition = Agence::find($colis->agence_expedition_id);

        foreach ($nouveauxColis as $index => $item) {
            $quantiteNecessaire = $item['quantite'];
            $quantiteExistante = 0;

            // Compter combien d'unités existent déjà pour ce produit
            foreach ($statutsIndividuels as $statut) {
                if ($statut['produit'] === $item['produit']) {
                    $quantiteExistante++;
                }
            }

            // Ajouter les unités manquantes
            for ($unite = $quantiteExistante + 1; $unite <= $quantiteNecessaire; $unite++) {
                $codeColisIndividuel = $this->genererCodeColisUnique();

                // Ajouter au tableau des codes individuels
                $codesColis['individuels'][] = [
                    'code_colis' => $codeColisIndividuel,
                    'colis_numero' => $index + 1,
                    'unite_numero' => $unite,
                    'produit' => $item['produit'],
                    'description' => $item['description'] ?? null
                ];

                // Créer le statut individuel
                $statutsIndividuels[$codeColisIndividuel] = [
                    'code_colis' => $codeColisIndividuel,
                    'colis_numero' => $index + 1,
                    'unite_numero' => $unite,
                    'produit' => $item['produit'],
                    'statut' => 'valide',
                    'date_creation' => now()->toDateTimeString(),
                    'date_modification' => now()->toDateTimeString(),
                    'localisation_actuelle' => $agenceExpedition->name,
                    'agence_actuelle_id' => $agenceExpedition->id,
                    'notes' => 'Colis ajouté lors de la mise à jour',
                    'historique' => [
                        [
                            'statut' => 'valide',
                            'date' => now()->toDateTimeString(),
                            'localisation' => $agenceExpedition->name,
                            'agence_id' => $agenceExpedition->id,
                            'notes' => 'Colis ajouté lors de la mise à jour'
                        ]
                    ]
                ];

                $compteurUnite++;
            }
        }
    }

    /**
     * Supprimer des statuts individuels
     */
    private function supprimerStatuts(&$statutsIndividuels, &$codesColis, $nombreASupprimer)
    {
        $codesASupprimer = array_slice(array_keys($statutsIndividuels), -$nombreASupprimer, $nombreASupprimer);

        foreach ($codesASupprimer as $code) {
            unset($statutsIndividuels[$code]);

            // Supprimer aussi du tableau des codes colis
            foreach ($codesColis['individuels'] as $key => $codeIndividuel) {
                if ($codeIndividuel['code_colis'] === $code) {
                    unset($codesColis['individuels'][$key]);
                    break;
                }
            }
        }

        // Réindexer les tableaux
        $codesColis['individuels'] = array_values($codesColis['individuels']);
    }

    /**
     * Mettre à jour le statut d'un colis individuel
     */
    public function updateStatutIndividuel(Request $request, $colisId, $codeColisIndividuel)
    {
        try {
            $colis = Colis::findOrFail($colisId);
            $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];

            if (!isset($statutsIndividuels[$codeColisIndividuel])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code colis individuel non trouvé'
                ], 404);
            }

            $validated = $request->validate([
                'statut' => 'required|in:valide,charge,entrepot,decharge,livre,annule',
                'localisation' => 'required|string|max:255',
                'agence_id' => 'nullable|exists:agences,id',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Mettre à jour le statut individuel
            $statutsIndividuels[$codeColisIndividuel]['statut'] = $validated['statut'];
            $statutsIndividuels[$codeColisIndividuel]['localisation_actuelle'] = $validated['localisation'];
            $statutsIndividuels[$codeColisIndividuel]['agence_actuelle_id'] = $validated['agence_id'];
            $statutsIndividuels[$codeColisIndividuel]['date_modification'] = now()->toDateTimeString();
            $statutsIndividuels[$codeColisIndividuel]['notes'] = $validated['notes'];

            // Ajouter à l'historique
            $statutsIndividuels[$codeColisIndividuel]['historique'][] = [
                'statut' => $validated['statut'],
                'date' => now()->toDateTimeString(),
                'localisation' => $validated['localisation'],
                'agence_id' => $validated['agence_id'],
                'notes' => $validated['notes']
            ];

            $colis->statuts_individuels = json_encode($statutsIndividuels);
            $colis->save();

            return response()->json([
                'success' => true,
                'message' => 'Statut individuel mis à jour avec succès',
                'statut' => $validated['statut']
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour statut individuel: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statuts individuels d'un colis
     */
    public function getStatutsIndividuels($id)
    {
        try {
            $colis = Colis::findOrFail($id);
            $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];

            return response()->json([
                'success' => true,
                'statuts_individuels' => $statutsIndividuels,
                'total_individuels' => count($statutsIndividuels)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statuts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour les détails spécifiques du paiement
     */
    private function updatePaiementDetails($colis, $request)
    {
        // Réinitialiser tous les champs de paiement
        $paiementFields = [
            'nom_banque',
            'numero_compte',
            'montant_virement',
            'operateur_mobile_money',
            'numero_mobile_money',
            'montant_mobile_money',
            'montant_espece',
            'montant_cheque',
            'montant_livraison'
        ];

        foreach ($paiementFields as $field) {
            $colis->$field = null;
        }

        // Mettre à jour selon la méthode de paiement
        switch ($request->methode_paiement) {
            case 'virement_bancaire':
                $colis->nom_banque = $request->nom_banque;
                $colis->numero_compte = $request->numero_compte;
                $colis->montant_virement = $request->montant_virement;
                break;

            case 'mobile_money':
                $colis->operateur_mobile_money = $request->operateur_mobile_money;
                $colis->numero_mobile_money = $request->numero_mobile_money;
                $colis->montant_mobile_money = $request->montant_mobile_money;
                break;

            case 'espece':
                $colis->montant_espece = $request->montant_espece;
                break;

            case 'cheque':
                $colis->montant_cheque = $request->montant_cheque;
                break;

            case 'livraison':
                $colis->montant_livraison = $request->montant_livraison ?? 0;
                break;
        }
    }

    // Méthodes auxiliaires
    private function calculerMontantColis($colis, $modeTransit = null)
    {
        $total = 0;
        foreach ($colis as $item) {
            $quantite = $item['quantite'] ?? 1;
            $prixUnitaire = $item['prix_unitaire'] ?? 0;

            // Calcul différent selon le mode de transit
            if ($modeTransit === 'Aerien') {
                // En mode aérien, le prix unitaire est déjà le total pour le colis (calculé par poids)
                // On ne multiplie PAS par la quantité
                $total += $prixUnitaire;
            } else {
                // En mode maritime, on multiplie par la quantité
                $total += $quantite * $prixUnitaire;
            }
        }
        return $total;
    }

    private function calculerQuantiteTotale($colis)
    {
        $total = 0;
        foreach ($colis as $item) {
            $total += $item['quantite'];
        }
        return $total;
    }

    private function genererCodeColis($quantiteTotale, $referenceColis)
    {
        do {
            $randomPart = strtoupper(Str::random(8)); // 8 caractères aléatoires
            $codeColis = 'CO-' . $randomPart;

            $exists = Colis::where('code_colis', 'LIKE', '%"' . $codeColis . '"%')->exists();
        } while ($exists);

        return $codeColis;
    }

    private function genererEtSauvegarderQRCode($colis, $reference, $codeColisPrincipal, $infos, $codesColisIndividuels)
    {
        $qrCodesData = [
            'code_colis_principal' => $codeColisPrincipal,
            'qr_individuels' => []
        ];

        // Créer le dossier pour les QR codes de ce colis
        $dossierColis = 'qrcodes/colis/' . $codeColisPrincipal;
        if (!Storage::disk('public')->exists($dossierColis)) {
            Storage::disk('public')->makeDirectory($dossierColis);
        }

        // Générer les QR codes INDIVIDUELS pour chaque unité de chaque colis
        $compteurUnite = 0;
        foreach ($codesColisIndividuels as $codeIndividuel) {
            $compteurUnite++;

            // UTILISER LE CODE COLIS INDIVIDUEL SPÉCIFIQUE
            $qrIndividuelData = $codeIndividuel['code_colis'];

            // Générer et sauvegarder le QR code individuel
            $qrIndividuelPath = $this->genererQRCodeImage($qrIndividuelData, $dossierColis, 'individuel_' . $compteurUnite);

            $qrCodesData['qr_individuels'][] = [
                'code_colis' => $codeIndividuel['code_colis'],
                'colis_numero' => $codeIndividuel['colis_numero'],
                'unite_numero' => $codeIndividuel['unite_numero'],
                'produit' => $codeIndividuel['produit'],
                'data' => $qrIndividuelData,
                'qr_code_path' => $qrIndividuelPath,
                'qr_code_url' => Storage::disk('public')->url($qrIndividuelPath)
            ];
        }

        return $qrCodesData;
    }

    private function genererCodeColisUnique()
    {
        // Format: CO-[8 caractères aléatoires]
        do {
            $randomPart = strtoupper(Str::random(8)); // 8 caractères aléatoires
            $codeColis = 'CO-' . $randomPart;

            // Vérifier si le code existe déjà dans la base
            $exists = Colis::where('code_colis', 'LIKE', '%"' . $codeColis . '"%')->exists();
        } while ($exists);

        return $codeColis;
    }

    private function genererQRCodeImage($data, $dossier, $nomFichier)
    {
        try {
            // $data est maintenant directement le code colis (string)
            $qrContent = $data;
            $codeColis = $data;

            // Générer le QR code avec SEULEMENT le code colis
            $qrCodeResult = Builder::create()
                ->writer(new PngWriter())
                ->data($qrContent) // Seul le code colis dans le QR
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->build();

            // Récupérer le contenu du QR code
            $qrCodeContent = $qrCodeResult->getString();

            // Nom du fichier
            $nomFichierComplet = $nomFichier . '.png';
            $cheminComplet = $dossier . '/' . $nomFichierComplet;

            // Sauvegarder dans le storage
            Storage::disk('public')->put($cheminComplet, $qrCodeContent);

            Log::info('QR code généré avec succès: ' . $cheminComplet . ' - Code: ' . $codeColis);

            return $cheminComplet;

        } catch (\Exception $e) {
            Log::error('Erreur génération QR code: ' . $e->getMessage());
            throw new \Exception('Impossible de générer le QR code: ' . $e->getMessage());
        }
    }

    private function sendNotificationToExpediteur(Colis $colis)
    {
        try {
            // Décoder les données QR
            $qrCodesData = json_decode($colis->qr_codes, true);

            // Préparer les données pour l'email
            $emailData = [
                'colis' => $colis,
                'code_colis_principal' => $qrCodesData['code_colis_principal'] ?? $colis->code_colis,
                'qr_codes' => $qrCodesData,
                'nombre_qr_codes' => count($qrCodesData['qr_individuels'] ?? []),
                'quantite_totale' => 0 // Plus de données globales
            ];

            // Envoyer l'email selon votre méthode habituelle
            $emails = [$colis->email_expediteur];

            foreach ($emails as $email) {
                // Créer un utilisateur temporaire pour la notification
                $tempUser = new User();
                $tempUser->email = $email;
                $tempUser->name = $colis->name_expediteur;

                // Vous devrez créer une notification spécifique pour les colis
                // $tempUser->notify(new ColisExpediteurNotification($emailData));
            }

            Log::info('Notification de colis envoyée à l\'expéditeur: ' . $colis->email_expediteur . ' - Code: ' . $colis->code_colis);

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification expéditeur colis: ' . $e->getMessage());
        }
    }

    public function searchUsers(Request $request)
    {
        try {
            $searchTerm = $request->get('q');

            if (empty($searchTerm)) {
                return response()->json([]);
            }

            $users = User::where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('prenom', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('contact', 'LIKE', "%{$searchTerm}%");
            })
                ->select('id', 'name', 'prenom', 'email', 'contact', 'adresse')
                ->limit(10)
                ->get();

            return response()->json($users);

        } catch (\Exception $e) {
            Log::error('Erreur recherche utilisateurs: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    // Dans AgentColisController
    public function getConteneurAndReference(Request $request)
    {
        try {
            $modeTransit = $request->get('mode_transit');

            if (!$modeTransit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mode de transit est requis'
                ], 400);
            }

            // Récupérer l'agent connecté et son agence
            $agent = Auth::guard('agent')->user();

            if (!$agent || !$agent->agence_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent non connecté ou aucune agence associée'
                ], 400);
            }

            $agenceExpedition = Agence::find($agent->agence_id);

            if (!$agenceExpedition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agence non trouvée pour cet agent'
                ], 404);
            }

            // Déterminer le type de conteneur selon le mode de transit
            $typeConteneur = ($modeTransit === 'Aerien') ? 'Ballon' : 'Conteneur';

            // Récupérer un conteneur ouvert pour CETTE AGENCE spécifique
            $conteneur = Conteneur::where('statut', 'ouvert')
                ->where('type_conteneur', $typeConteneur)
                ->where('agence_id', $agenceExpedition->id) // FILTRE PAR AGENCE
                ->orderBy('created_at', 'asc')
                ->first();

            // Si aucun conteneur ouvert pour cette agence, créer un nouveau
            if (!$conteneur) {
                $conteneur = new Conteneur();
                $conteneur->name_conteneur = $typeConteneur . ' - ' . $agenceExpedition->name . ' - ' . date('Y-m-d');
                $conteneur->type_conteneur = $typeConteneur;
                $conteneur->statut = 'ouvert';
                $conteneur->agence_id = $agenceExpedition->id; // ASSOCIER LE CONTENEUR À L'AGENCE
                $conteneur->save();
            }

            // Générer la référence avec l'agence de l'agent
            $initiales = $this->genererInitiales($agent->name, $agent->prenom);
            $reference = $this->genererReference($initiales, $modeTransit, $agenceExpedition->id);

            // Récupérer les agences de destination
            $agencesDestination = Agence::where('pays', 'Côte d\'Ivoire')->get();

            return response()->json([
                'success' => true,
                'conteneur' => $conteneur,
                'reference' => $reference,
                'agencesDestination' => $agencesDestination,
                'agenceExpedition' => $agenceExpedition
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur dans getConteneurAndReference: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    private function genererReference($initiales, $modeTransit, $agenceExpeditionId = null)
    {
        // Déterminer le type de conteneur selon le mode de transit
        $typeConteneur = ($modeTransit === 'Aerien') ? 'Ballon' : 'Conteneur';

        if ($typeConteneur === 'Ballon') {
            // NOUVELLE LOGIQUE : Trouver le prochain suffixe disponible
            if ($agenceExpeditionId) {
                $suffixe = $this->trouverProchainSuffixeBallon($agenceExpeditionId);
            } else {
                // Pour le mode sans agence spécifique
                $nombreConteneursFermes = Conteneur::where('statut', 'fermer')
                    ->where('type_conteneur', 'Ballon')
                    ->count();
                $suffixe = 'A' . ($nombreConteneursFermes + 1);
            }
        } else {
            // Logique pour les conteneurs maritimes (inchangée)
            if ($agenceExpeditionId) {
                $nombreConteneursAgence = Colis::where('agence_expedition_id', $agenceExpeditionId)
                    ->where('mode_transit', 'Maritime')
                    ->join('conteneurs', 'colis.conteneur_id', '=', 'conteneurs.id')
                    ->where('conteneurs.statut', 'fermer')
                    ->distinct('conteneurs.id')
                    ->count('conteneurs.id');
                $suffixe = 'TC' . ($nombreConteneursAgence + 1);
            } else {
                $nombreConteneursFermes = Conteneur::where('statut', 'fermer')
                    ->where('type_conteneur', 'Conteneur')
                    ->count() + 1;
                $suffixe = 'TC' . $nombreConteneursFermes;
            }
        }

        // Trouver le prochain incrément disponible pour ce suffixe
        $prochainIncrement = $this->trouverProchainIncrementDisponible($suffixe, $modeTransit, $agenceExpeditionId);
        $incrementColis = str_pad($prochainIncrement, 4, '0', STR_PAD_LEFT);

        return $initiales . '-' . $incrementColis . '-' . $suffixe;
    }

    // Méthode pour afficher un QR code
    public function showQRCode($colisId, $numero)
    {
        $colis = Colis::findOrFail($colisId);
        $qrCodesData = json_decode($colis->qr_codes, true);

        $qrCodePath = null;

        // Rechercher le QR code individuel par numéro
        foreach ($qrCodesData['qr_individuels'] as $qrIndividuel) {
            if ($qrIndividuel['unite_numero'] == $numero) {
                $qrCodePath = $qrIndividuel['qr_code_path'];
                break;
            }
        }

        if ($qrCodePath && Storage::disk('public')->exists($qrCodePath)) {
            return response()->file(Storage::disk('public')->path($qrCodePath));
        }

        abort(404, 'QR code non trouvé');
    }

    // Dans votre ColisController, ajoutez cette méthode
    private function trouverProchainSuffixeBallon($agenceExpeditionId)
    {
        // Compter le nombre de conteneurs "Ballon" FERMÉS associés à cette agence via les colis
        $nombreConteneursFermes = Colis::where('agence_expedition_id', $agenceExpeditionId)
            ->where('mode_transit', 'Aerien')
            ->join('conteneurs', 'colis.conteneur_id', '=', 'conteneurs.id')
            ->where('conteneurs.statut', 'fermer')
            ->distinct('conteneurs.id')
            ->count('conteneurs.id');

        // Le suffixe est A + (nombre de fermés + 1)
        // Ex: 0 fermé -> A1
        // Ex: 1 fermé -> A2
        return 'A' . ($nombreConteneursFermes + 1);
    }

    public function show($id)
    {
        try {
            $colis = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur', 'service'])
                ->findOrFail($id);

            // Décoder les données des colis (déjà casté en array)
            $colisDetails = $colis->colis;
            $nombreTypesColis = is_array($colisDetails) ? count($colisDetails) : 0;

            // Décoder les statuts individuels
            $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];
            $codesColis = json_decode($colis->code_colis, true) ?? [];

            // Compter les statuts
            $compteurStatuts = [
                'valide' => 0,
                'charge' => 0,
                'entrepot' => 0,
                'decharge' => 0,
                'livre' => 0,
                'annule' => 0
            ];

            // Récupérer tous les IDs de conteneurs uniques dans les statuts individuels
            $conteneurIds = [];
            foreach ($statutsIndividuels as $statut) {
                if (isset($statut['localisation_actuelle']) && preg_match('/Conteneur #(\d+)/', $statut['localisation_actuelle'], $matches)) {
                    $conteneurIds[] = $matches[1];
                }
            }

            if (!empty($conteneurIds)) {
                $conteneursMap = Conteneur::whereIn('id', array_unique($conteneurIds))->pluck('name_conteneur', 'id');
                foreach ($statutsIndividuels as &$statutIndiv) {
                    if (isset($statutIndiv['localisation_actuelle']) && preg_match('/Conteneur #(\d+)/', $statutIndiv['localisation_actuelle'], $matches)) {
                        $conteneur_id_match = $matches[1];
                        if (isset($conteneursMap[$conteneur_id_match])) {
                            $statutIndiv['localisation_actuelle'] = str_replace('Conteneur #' . $conteneur_id_match, $conteneursMap[$conteneur_id_match], $statutIndiv['localisation_actuelle']);
                        }
                    }

                    if (isset($compteurStatuts[$statutIndiv['statut']])) {
                        $compteurStatuts[$statutIndiv['statut']]++;
                    }
                }
                unset($statutIndiv);
            } else {
                foreach ($statutsIndividuels as $statut) {
                    if (isset($compteurStatuts[$statut['statut']])) {
                        $compteurStatuts[$statut['statut']]++;
                    }
                }
            }

            // Préparer les données des colis
            $colisData = [
                'id' => $colis->id,
                'reference_colis' => $colis->reference_colis,
                'code_colis' => $codesColis,
                'mode_transit' => $colis->mode_transit,
                'devise' => $colis->devise,
                'name_expediteur' => $colis->name_expediteur,
                'prenom_expediteur' => $colis->prenom_expediteur,
                'email_expediteur' => $colis->email_expediteur,
                'contact_expediteur' => $colis->contact_expediteur,
                'adresse_expediteur' => $colis->adresse_expediteur,
                'name_destinataire' => $colis->name_destinataire,
                'prenom_destinataire' => $colis->prenom_destinataire,
                'email_destinataire' => $colis->email_destinataire,
                'indicatif' => $colis->indicatif,
                'contact_destinataire' => $colis->contact_destinataire,
                'adresse_destinataire' => $colis->adresse_destinataire,
                'agence_expedition' => $colis->agence_expedition,
                'agence_destination' => $colis->agence_destination,
                'statut' => $colis->statut,
                'statut_paiement' => $colis->statut_paiement,
                'montant_total' => $colis->montant_total,
                'montant_paye' => $colis->montant_paye,
                'reste_a_payer' => $colis->reste_a_payer,
                'methode_paiement' => $colis->methode_paiement,
                'montant_colis' => $colis->montant_colis,
                'created_at' => $colis->created_at->format('d/m/Y H:i'),
                'nombre_types_colis' => $nombreTypesColis,
                'colis_details' => is_array($colisDetails) ? $colisDetails : [],
                'statuts_individuels' => $statutsIndividuels,
                'compteur_statuts' => $compteurStatuts,
                'total_individuels' => count($statutsIndividuels)
            ];

            return response()->json($colisData);

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des détails du colis (ID: ' . $id . '): ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enregistrerPaiement(Request $request, $id)
    {
        try {
            $colis = Colis::findOrFail($id);

            // Validation des données
            $validated = $request->validate([
                'montant' => 'required|numeric|min:0.01',
                'methode_paiement' => 'required|in:espece,virement_bancaire,cheque,mobile_money',
                'nom_banque' => 'nullable|required_if:methode_paiement,virement_bancaire|string|max:255',
                'numero_compte' => 'nullable|required_if:methode_paiement,virement_bancaire|string|max:255',
                'operateur' => 'nullable|required_if:methode_paiement,mobile_money|in:WAVE,ORANGE,MOOV,MTN',
                'numero_mobile' => 'nullable|required_if:methode_paiement,mobile_money|string|max:255',
                'notes' => 'nullable|string'
            ]);

            $montantPaiement = $validated['montant'];
            $nouveauMontantPaye = $colis->montant_paye + $montantPaiement;
            $resteAPayer = $colis->montant_total - $nouveauMontantPaye;

            // Déterminer le nouveau statut de paiement
            if ($resteAPayer <= 0) {
                $statutPaiement = 'totalement_paye';
            } elseif ($nouveauMontantPaye > 0) {
                $statutPaiement = 'partiellement_paye';
            } else {
                $statutPaiement = 'non_paye';
            }

            // Mettre à jour le colis
            $colis->update([
                'montant_paye' => $nouveauMontantPaye,
                'reste_a_payer' => max(0, $resteAPayer),
                'statut_paiement' => $statutPaiement,
                'methode_paiement' => $validated['methode_paiement'],
                'nom_banque' => $validated['nom_banque'] ?? $colis->nom_banque,
                'numero_compte' => $validated['numero_compte'] ?? $colis->numero_compte,
                'operateur_mobile_money' => $validated['operateur'] ?? $colis->operateur_mobile_money,
                'numero_mobile_money' => $validated['numero_mobile'] ?? $colis->numero_mobile_money,
                'notes_paiement' => $validated['notes'] ?
                    ($colis->notes_paiement ? $colis->notes_paiement . "\n" . $validated['notes'] : $validated['notes'])
                    : $colis->notes_paiement,
                'agent_encaisseur_id' => Auth::guard('agent')->id(),
                'agent_encaisseur_type' => 'agent',
                'agent_encaisseur_name' => Auth::guard('agent')->user()->name,
                'date_paiement' => now(),
            ]);

            // Créer l'historique du paiement
            Paiement::create([
                'colis_id' => $colis->id,
                'montant' => $montantPaiement,
                'methode_paiement' => $validated['methode_paiement'],
                'nom_banque' => $validated['nom_banque'],
                'numero_compte' => $validated['numero_compte'],
                'operateur_mobile_money' => $validated['operateur'],
                'numero_mobile_money' => $validated['numero_mobile'],
                'notes' => $validated['notes'],
                'agent_id' => Auth::guard('agent')->id(),
                'agent_type' => 'agent',
                'agent_name' => Auth::guard('agent')->user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès',
                'nouveau_montant_paye' => $nouveauMontantPaye,
                'reste_a_payer' => $resteAPayer,
                'statut_paiement' => $statutPaiement
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur enregistrement paiement colis: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();

        if (!$agent || !$agent->agence_id) {
            $colis = collect()->paginate(10);
            return view('agent.colis.history', compact('colis'));
        }

        $query = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
            ->where('statut', '!=', 'valide')
            ->where('agence_expedition_id', $agent->agence_id) // Utiliser l'agence de l'agent
            ->orderBy('created_at', 'desc');

        // Filtre de recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_colis', 'LIKE', "%{$search}%")
                    ->orWhere('name_expediteur', 'LIKE', "%{$search}%")
                    ->orWhere('name_destinataire', 'LIKE', "%{$search}%")
                    ->orWhere('email_expediteur', 'LIKE', "%{$search}%")
                    ->orWhere('email_destinataire', 'LIKE', "%{$search}%")
                    ->orWhere('code_colis', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->has('status') && !empty($request->status)) {
            $query->where('statut', $request->status);
        }

        // Filtre par mode de transit
        if ($request->has('mode_transit') && !empty($request->mode_transit)) {
            $query->where('mode_transit', $request->mode_transit);
        }

        // Filtre par statut de paiement
        if ($request->has('paiement') && !empty($request->paiement)) {
            $query->where('statut_paiement', $request->paiement);
        }

        $colis = $query->paginate(10);

        // Compter le nombre de tableaux (types de colis) dans le champ colis (JSON)
        $colis->getCollection()->transform(function ($item) {
            $colisData = json_decode($item->colis, true);
            $item->nombre_types_colis = is_array($colisData) ? count($colisData) : 0;
            return $item;
        });

        return view('agent.colis.history', compact('colis'));
    }

    public function destroy($id)
    {
        try {
            $colis = Colis::findOrFail($id);

            // Ajoutez ici toute logique de suppression supplémentaire si nécessaire

            $colis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Colis supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du colis'
            ], 500);
        }
    }
    /**
     * Rechercher un client (expéditeur ou destinataire) dans l'historique des colis
     */
    public function searchClient(Request $request)
    {
        $search = $request->query('q');
        $type = $request->query('type', 'expediteur'); // 'expediteur' ou 'destinataire'

        if (!$search || strlen($search) < 2) {
            return response()->json([]);
        }

        $query = Colis::query();

        // Sélectionner les champs pertinents selon le type
        if ($type === 'expediteur') {
            $query->selectRaw('
                DISTINCT
                name_expediteur as name,
                prenom_expediteur as prenom,
                email_expediteur as email,
                contact_expediteur as contact,
                adresse_expediteur as adresse
            ')
                ->where(function ($q) use ($search) {
                    $q->where('name_expediteur', 'LIKE', "%{$search}%")
                        ->orWhere('prenom_expediteur', 'LIKE', "%{$search}%")
                        ->orWhere('contact_expediteur', 'LIKE', "%{$search}%");
                });
        } else {
            $query->selectRaw('
                DISTINCT
                name_destinataire as name,
                prenom_destinataire as prenom,
                email_destinataire as email,
                contact_destinataire as contact,
                adresse_destinataire as adresse,
                indicatif
            ')
                ->where(function ($q) use ($search) {
                    $q->where('name_destinataire', 'LIKE', "%{$search}%")
                        ->orWhere('prenom_destinataire', 'LIKE', "%{$search}%")
                        ->orWhere('contact_destinataire', 'LIKE', "%{$search}%");
                });
        }

        $clients = $query->limit(10)->get();

        return response()->json($clients);
    }
}

