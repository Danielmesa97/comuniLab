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
        // Columna para saber a qué PISO le hemos delegado el voto
        $table->foreignId('vivienda_delegada_id')->nullable()->constrained('viviendas')->onDelete('set null');
        
        // Cambiamos 'opcion' para que pueda ser NULL mientras el voto esté delegado pero no ejecutado
        $table->string('opcion')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('votos', function (Blueprint $table) {
        $table->dropForeign(['vivienda_delegada_id']);
        $table->dropColumn('vivienda_delegada_id');
        $table->string('opcion')->nullable(false)->change();
    });
}
};
