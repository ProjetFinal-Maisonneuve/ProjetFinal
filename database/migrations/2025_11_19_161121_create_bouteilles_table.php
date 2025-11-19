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
        Schema::create('bouteilles', function (Blueprint $table) {
            $table->id();

            // clé étrangère vers le cellier
            $table->foreignId('cellier_id')
                ->constrained('celliers')
                ->onDelete('cascade');

            $table->string('nom');            
            $table->string('pays')->nullable();
            $table->string('format', 50)->nullable();
            $table->unsignedInteger('quantite');
            $table->decimal('prix', 8, 2);   
            $table->timestamps();             
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouteilles');
    }
};
