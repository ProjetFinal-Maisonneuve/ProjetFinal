<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Supprime la table bouteille_cellier qui n'a jamais été utilisée.
     * Le système utilise la table bouteilles avec le champ code_saq pour différencier
     * les bouteilles SAQ des bouteilles manuelles.
     */
    public function up(): void
    {
        if (Schema::hasTable('bouteille_cellier')) {
            Schema::dropIfExists('bouteille_cellier');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table si nécessaire (copie de la migration originale)
        Schema::create('bouteille_cellier', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_cellier')
                ->constrained('celliers')
                ->onDelete('cascade');
            
            $table->foreignId('id_bouteille_catalogue')
                ->constrained('bouteille_catalogue')
                ->onDelete('cascade');
            
            $table->unsignedInteger('quantite')->default(1);
            $table->text('note_degustation')->nullable();
            $table->dateTime('date_ajout')->useCurrent();
            $table->date('date_ouverture')->nullable();
            $table->boolean('achetee_non_listee')->default(false);
        });
    }
};
