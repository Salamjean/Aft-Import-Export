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
       Schema::create('bateaus', function (Blueprint $table) {
            $table->id();
            $table->string('type_transport');
            $table->foreignId('conteneur_id')->constrained('conteneurs')->onDelete('cascade');
            $table->string('reference');
            $table->string('date_arrive');
            $table->string('compagnie')->nullable();
            $table->string('nom')->nullable();
            $table->string('numero')->nullable();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->enum('statut', ['depart', 'en_cours','arrive'])->default('en_cours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bateaus');
    }
};
