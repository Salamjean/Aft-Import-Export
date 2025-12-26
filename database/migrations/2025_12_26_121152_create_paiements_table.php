<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colis_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->string('methode_paiement');
            $table->string('nom_banque')->nullable();
            $table->string('numero_compte')->nullable();
            $table->string('operateur_mobile_money')->nullable();
            $table->string('numero_mobile_money')->nullable();
            $table->text('notes')->nullable();

            // Suivi de l'agent qui a encaissé
            $table->unsignedBigInteger('agent_id');
            $table->string('agent_type'); // admin ou agent
            $table->string('agent_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
