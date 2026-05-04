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
    Schema::table('votos', function (Blueprint $table) {
        $table->foreignId('vivienda_id')->constrained('viviendas')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('votos', function (Blueprint $table) {
        $table->dropForeign(['vivienda_id']);
        $table->dropColumn('vivienda_id');

        $table->foreignId('user_id')->nullable();
    });
}
};
