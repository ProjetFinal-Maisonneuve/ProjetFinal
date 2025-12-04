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
        Schema::table('bouteille_catalogue', function (Blueprint $table) {
            $table->string('url_image_thumbnail', 255)->nullable()->after('url_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bouteille_catalogue', function (Blueprint $table) {
            $table->dropColumn('url_image_thumbnail');
        });
    }
};
