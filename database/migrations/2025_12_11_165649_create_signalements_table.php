<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();

            // Liaison obligatoire vers bouteille_catalogue
            $table->foreignId('bouteille_catalogue_id')
                ->constrained('bouteille_catalogue')
                ->onDelete('cascade');

            // Champs obligatoires
            $table->string('nom');        // Titre du signalement (obligatoire)
            $table->text('description');  // Description complète (obligatoire)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};
