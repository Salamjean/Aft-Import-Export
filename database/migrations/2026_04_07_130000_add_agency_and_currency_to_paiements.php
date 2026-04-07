<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Mise à jour de la structure
        Schema::table('paiements', function (Blueprint $table) {
            $table->unsignedBigInteger('agence_id')->nullable()->after('notes');
            $table->string('devise', 10)->nullable()->after('agence_id');
            
            $table->foreign('agence_id')->references('id')->on('agences')->onDelete('set null');
        });

        // 2. Synchronisation des données existantes
        $paiements = DB::table('paiements')->get();
        $parisAgence = DB::table('agences')
            ->where('name', 'LIKE', '%Paris%')
            ->orWhere('name', 'LIKE', '%Siège%')
            ->first();

        foreach ($paiements as $p) {
            $agenceId = null;
            $devise = 'XOF';

            if ($p->agent_type === 'agent') {
                // Récupérer l'agence de l'agent
                $agent = DB::table('agents')->where('id', $p->agent_id)->first();
                if ($agent) {
                    $agenceId = $agent->agence_id;
                }
            } else {
                // Admin : On utilise l'agence de Paris par défaut ou celle du colis original
                $agenceId = $parisAgence ? $parisAgence->id : null;
            }

            // Si toujours pas d'agence, on utilise l'agence expédition du colis pour ne pas perdre la donnée
            if (!$agenceId) {
                $colis = DB::table('colis')->where('id', $p->colis_id)->first();
                if ($colis) {
                    $agenceId = $colis->agence_expedition_id;
                }
            }

            // On récupère la devise de l'agence attribuée
            if ($agenceId) {
                $agence = DB::table('agences')->where('id', $agenceId)->first();
                if ($agence) {
                    $devise = $agence->devise ?? 'XOF';
                }
            }

            DB::table('paiements')->where('id', $p->id)->update([
                'agence_id' => $agenceId,
                'devise' => $devise
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['agence_id']);
            $table->dropColumn(['agence_id', 'devise']);
        });
    }
};
