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
       Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('email');

            $table->string('role'); // inquilino, propietario, etc
            $table->foreignId('vivienda_id')->constrained('viviendas');

            $table->foreignId('comunidad_id')->constrained('comunidades');

            $table->enum('estado', ['pendiente','aceptada','rechazada'])->default('pendiente');

            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicituds');
    }
};
