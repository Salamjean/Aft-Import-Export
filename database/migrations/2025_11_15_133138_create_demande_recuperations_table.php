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
        Schema::create('demande_recuperations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agence_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantite')->default(1);
            $table->string('reference');
            $table->string('nature_objet');
            $table->string('nom_concerne');
            $table->string('prenom_concerne');
            $table->string('contact');
            $table->enum('statut',['en_attente','traite','annule'])->default('en_attente');
            $table->string('email')->nullable();
            $table->text('adresse_recuperation');
            $table->timestamp('date_recuperation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_recuperations');
    }
};
