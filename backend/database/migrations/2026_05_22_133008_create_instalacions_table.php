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
        Schema::create('instalaciones', function (Blueprint $table) {
            $table->id();
            // Relación con la comunidad (una instalación pertenece a una comunidad)
            $table->foreignId('comunidad_id')->constrained('comunidades')->onDelete('cascade');
            
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('duracion_franja'); // Ej: 90 (minutos)
            $table->integer('aforo_max');       // Ej: 4 (personas)
            $table->string('icono')->nullable(); // Ej: '🏓'
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instalacions');
    }
};
