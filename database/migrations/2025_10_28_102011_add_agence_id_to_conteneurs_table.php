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
        Schema::table('conteneurs', function (Blueprint $table) {
            $table->foreignId('agence_id')->nullable()->after('statut');
            
            // Ajouter la contrainte de clé étrangère
            $table->foreign('agence_id')
                  ->references('id')
                  ->on('agences')
                  ->onDelete('cascade');
                  
            // Index pour améliorer les performances
            $table->index(['statut', 'agence_id', 'type_conteneur']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conteneurs', function (Blueprint $table) {
             // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['agence_id']);
            
            // Supprimer la colonne
            $table->dropColumn('agence_id');
            
            // Supprimer l'index
            $table->dropIndex(['statut', 'agence_id', 'type_conteneur']);
        });
    }
};
