<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('colis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conteneur_id')->constrained('conteneurs')->onDelete('cascade');
            $table->string('reference_colis')->nullable();
            $table->string('mode_transit');
            $table->foreignId('agence_expedition_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('agence_destination_id')->constrained('agences')->onDelete('cascade');
            $table->string('agence_destination');
            $table->string('agence_expedition');
            $table->string('devise');

            // Informations de l'expediteur 
            $table->string('name_expediteur');
            $table->string('prenom_expediteur')->nullable();
            $table->string('email_expediteur')->nullable();
            $table->string('contact_expediteur');
            $table->string('adresse_expediteur');

            // Informations du destinataire 
            $table->string('name_destinataire');
            $table->string('prenom_destinataire');
            $table->string('email_destinataire')->nullable();
            $table->string('indicatif');
            $table->string('contact_destinataire');
            $table->string('adresse_destinataire');

            $table->json('colis'); // Stocke tous les colis sous forme de JSON
            $table->decimal('montant_colis')->nullable();
            $table->decimal('montant_paye_colis')->nullable();
            

            // Lien avec l'utilisateur
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // Statut du devis
            $table->enum('statut', ['valide', 'charge', 'entrepot', 'decharge', 'livre', 'annule'])->default('valide');
            $table->json('statuts_individuels')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colis');
    }
};
