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
        Schema::create('conteneurs', function (Blueprint $table) {
            $table->id();
            $table->string('name_conteneur');
            $table->string('type_conteneur');
            $table->enum('statut', ['ouvert', 'fermer'])->default('ouvert');
            $table->string('numero_conteneur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conteneurs');
    }
};
