<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunidad_user', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('comunidad_id')
                ->constrained('comunidades')
                ->onDelete('cascade');

            $table->string('role');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunidad_user');
    }
};