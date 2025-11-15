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
            $table->string('nom_destinataire')->nullable();
            $table->string('prenom_destinataire')->nullable();
            $table->string('email_destinataire')->nullable();
            $table->string('indicatif_destinataire')->nullable();
            $table->string('contact_destinataire')->nullable();
            $table->text('adresse_destinataire')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recuperations', function (Blueprint $table) {
            $table->dropColumn([
                'nom_destinataire',
                'prenom_destinataire',
                'email_destinataire',
                'indicatif_destinataire',
                'contact_destinataire',
                'adresse_destinataire'
            ]);
        });
    }
};
