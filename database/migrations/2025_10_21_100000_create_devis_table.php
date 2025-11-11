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
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            
            // Informations de transport
            $table->string('reference_devis')->nullable();
            $table->string('mode_transit');
            $table->foreignId('agence_expedition_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('agence_destination_id')->constrained('agences')->onDelete('cascade');
            $table->string('agence_destination');
            $table->string('agence_expedition');
            $table->string('pays_expedition');
            $table->string('devise');

            // Informations du client 
            $table->string('name_client');
            $table->string('prenom_client');
            $table->string('email_client');
            $table->string('contact_client');
            $table->string('adresse_client');

            $table->json('colis'); // Stocke tous les colis sous forme de JSON
            $table->decimal('montant_devis')->nullable();

            // Lien avec l'utilisateur
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Statut du devis
            $table->enum('statut', ['en_attente', 'traite', 'annule'])->default('en_attente');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
