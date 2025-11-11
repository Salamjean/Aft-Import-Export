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
        Schema::table('colis', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->decimal('prix_service')->nullable();
            $table->decimal('montant_total')->nullable();
            $table->decimal('montant_paye')->nullable();
            $table->decimal('reste_a_payer')->nullable();
            $table->enum('statut_paiement', ['non_paye', 'partiellement_paye', 'totalement_paye'])->default('non_paye');
            $table->text('notes_paiement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn([
                'service_id', 'prix_service', 'montant_total', 
                'montant_paye', 'reste_a_payer', 'statut_paiement', 'notes_paiement'
            ]);
        });
    }
};
