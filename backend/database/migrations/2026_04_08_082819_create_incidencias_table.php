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
    Schema::create('incidencias', function (Blueprint $table) {
        $table->id();
        
        // Datos básicos de la incidencia
        $table->string('titulo');
        $table->text('descripcion');
        
        // El estado por defecto será 'pendiente'
        $table->enum('estado', ['pendiente', 'en progreso', 'resuelta'])->default('pendiente');
        
        // Relaciones (Claves foráneas)
        // Asume que tendréis tablas 'users' y 'comunidades'
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // $table->foreignId('comunidad_id')->constrained()->onDelete('cascade'); // Descomenta esto cuando crees la tabla comunidades
        
        // Crea los campos created_at y updated_at automáticamente
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
