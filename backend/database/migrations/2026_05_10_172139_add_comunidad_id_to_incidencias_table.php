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
    Schema::table('incidencias', function (Blueprint $table) {
        $table->foreignId('comunidad_id')
            ->nullable()
            ->after('user_id')
            ->constrained('comunidades')
            ->nullOnDelete();
    });
}

public function down()
{
    Schema::table('incidencias', function (Blueprint $table) {
        $table->dropConstrainedForeignId('comunidad_id');
    });
}
};
