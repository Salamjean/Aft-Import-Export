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
            $table->string('methode_paiement')->nullable();
            $table->string('nom_banque')->nullable();
            $table->string('numero_compte')->nullable();
            $table->string('operateur_mobile_money')->nullable();
            $table->string('numero_mobile_money')->nullable();
            $table->decimal('montant_espece', 10, 2)->nullable();
            $table->decimal('montant_virement', 10, 2)->nullable();
            $table->decimal('montant_cheque', 10, 2)->nullable();
            $table->decimal('montant_mobile_money', 10, 2)->nullable();
            $table->decimal('montant_livraison', 10, 2)->nullable();
            $table->json('qr_codes')->nullable()->after('notes_paiement');
            $table->json('code_colis')->nullable()->after('reference_colis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colis', function (Blueprint $table) {
           $table->dropColumn([
                'methode_paiement',
                'nom_banque', 
                'numero_compte',
                'operateur_mobile_money',
                'numero_mobile_money',
                'montant_espece',
                'montant_virement',
                'montant_cheque',
                'montant_mobile_money',
                'montant_livraison',
                'qr_codes',
                'code_colis'
            ]);
        });
    }
};
