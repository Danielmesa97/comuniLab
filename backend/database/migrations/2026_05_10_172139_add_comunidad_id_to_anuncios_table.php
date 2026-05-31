<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('anuncios', function (Blueprint $table) {
        $table->foreignId('comunidad_id')
            ->nullable()
            ->after('fecha_fin')
            ->constrained('comunidades')
            ->nullOnDelete();
    });
}

public function down()
{
    Schema::table('anuncios', function (Blueprint $table) {
        $table->dropConstrainedForeignId('comunidad_id');
    });
}
};
