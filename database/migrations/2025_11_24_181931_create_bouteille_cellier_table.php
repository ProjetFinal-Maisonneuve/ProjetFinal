<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @deprecated Cette migration a été remplacée par l'utilisation de la table bouteilles.
 * La table bouteille_cellier n'a jamais été utilisée dans le code.
 * Cette migration sera annulée par: 2025_12_04_221556_drop_bouteille_cellier_table_if_exists
 * 
 * Le système utilise maintenant la table bouteilles avec le champ code_saq pour différencier
 * les bouteilles du catalogue SAQ (code_saq rempli) des bouteilles manuelles (code_saq NULL).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * @deprecated Ne pas utiliser cette table. Utiliser bouteilles à la place.
     */
    public function up(): void
    {
        // Migration désactivée - cette table n'est plus utilisée
        // Le système utilise la table bouteilles avec code_saq pour différencier SAQ/manuelles
        
        // Schema::create('bouteille_cellier', function (Blueprint $table) {
        //     $table->id();
        //     
        //     $table->foreignId('id_cellier')
        //         ->constrained('celliers')
        //         ->onDelete('cascade');
        //     
        //     $table->foreignId('id_bouteille_catalogue')
        //         ->constrained('bouteille_catalogue')
        //         ->onDelete('cascade');
        //     
        //     $table->unsignedInteger('quantite')->default(1);
        //     $table->text('note_degustation')->nullable();
        //     $table->dateTime('date_ajout')->useCurrent();
        //     $table->date('date_ouverture')->nullable();
        //     $table->boolean('achetee_non_listee')->default(false);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouteille_cellier');
    }
};
