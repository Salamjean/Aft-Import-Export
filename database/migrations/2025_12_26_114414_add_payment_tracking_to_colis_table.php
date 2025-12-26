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
        Schema::table('colis', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_encaisseur_id')->nullable();
            $table->string('agent_encaisseur_type')->nullable(); // admin ou agent
            $table->string('agent_encaisseur_name')->nullable();
            $table->dateTime('date_paiement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->dropColumn(['agent_encaisseur_id', 'agent_encaisseur_type', 'agent_encaisseur_name', 'date_paiement']);
        });
    }
};
