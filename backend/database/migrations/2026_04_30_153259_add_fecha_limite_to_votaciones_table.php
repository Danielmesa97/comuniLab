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
        Schema::table('votaciones', function (Blueprint $table) {
            // Usamos dateTime para precisión de hora y minutos
            // nullable() por si quieres que algunas votaciones sean eternas
            $table->dateTime('fecha_limite')->after('estado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votaciones', function (Blueprint $table) {
            //
        });
    }
};
