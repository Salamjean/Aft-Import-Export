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
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->integer('quantite')->default(1);
            $table->string('nature_objet');
            $table->string('nom_concerne');
            $table->string('prenom_concerne');
            $table->string('contact');
            $table->string('email')->nullable();
            $table->text('adresse_livraison'); 
            $table->foreignId('chauffeur_id')->constrained('chauffeurs')->onDelete('cascade');
            $table->timestamp('date_livraison')->nullable();
            $table->enum('statut', ['programme', 'en_cours', 'termine', 'annule'])->default('en_cours');
            $table->timestamps();
            
            $table->index('reference');
            $table->index('chauffeur_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};
