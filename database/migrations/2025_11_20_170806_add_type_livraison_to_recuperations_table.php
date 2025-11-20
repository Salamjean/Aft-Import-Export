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
        Schema::table('recuperations', function (Blueprint $table) {
             $table->enum('type_livraison', ['livraison', 'enlevement'])->nullable()->after('adresse_destinataire');
             $table->text('lieu_livraison')->nullable()->after('type_livraison');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recuperations', function (Blueprint $table) {
            $table->dropColumn(['type_livraison', 'lieu_livraison']);
        });
    }
};
