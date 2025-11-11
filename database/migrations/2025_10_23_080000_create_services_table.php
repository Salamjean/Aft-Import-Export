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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->decimal('prix_unitaire', 10, 2);
            $table->foreignId('agence_destination_id')->constrained('agences')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->enum('type_service', ['obligatoire', 'optionnel'])->default('optionnel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
