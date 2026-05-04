<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear Comunidad[cite: 1]
        $comunidadId = DB::table('comunidades')->insertGetId([
            'nombre' => 'Residencial Los Pinos',
            'descripcion' => 'Comunidad de vecinos de la zona norte',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Crear Viviendas asociadas a la comunidad[cite: 1]
        $vivienda1Id = DB::table('viviendas')->insertGetId([
            'nombre' => '1º A',
            'comunidad_id' => $comunidadId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $vivienda2Id = DB::table('viviendas')->insertGetId([
            'nombre' => '1º B',
            'comunidad_id' => $comunidadId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Crear Usuarios (asignando rol, vivienda_id y activo)[cite: 1]
        $user1Id = DB::table('users')->insertGetId([
            'name' => 'Toni Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'propietario', // o 'admin' si lo manejas
            'vivienda_id' => $vivienda1Id,
            'activo' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $user2Id = DB::table('users')->insertGetId([
            'name' => 'Laura Inquilina',
            'email' => 'laura@test.com',
            'password' => Hash::make('password123'),
            'role' => 'inquilino',
            'vivienda_id' => $vivienda2Id,
            'activo' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 4. Crear Votaciones[cite: 1]
        $votacionId = DB::table('votaciones')->insertGetId([
            'titulo' => 'Pintar la fachada del edificio',
            'descripcion' => 'Se propone pintar la fachada de color blanco para mejorar la estética.',
            'estado' => 'activa',
            'fecha_limite' => Carbon::now()->addDays(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 5. Crear Votos (¡Requiere que hayas arreglado la migración de vivienda_id!)[cite: 1]
        DB::table('votos')->insert([
            [
                'user_id' => $user1Id,
                'votacion_id' => $votacionId,
                'opcion' => 'si',
                // Descomenta la siguiente línea una vez arregles tu migración:
                // 'vivienda_id' => $vivienda1Id, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 6. Crear Incidencias[cite: 1]
        DB::table('incidencias')->insert([
            'titulo' => 'Luz fundida en el pasillo',
            'descripcion' => 'La luz del primer piso lleva dos días sin funcionar.',
            'estado' => 'pendiente',
            'user_id' => $user1Id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 7. Crear Anuncios (tipos válidos: noticia, evento, aviso, documento)[cite: 1]
        DB::table('anuncios')->insert([
            'titulo' => 'Reunión Anual',
            'descripcion' => 'Reunión de vecinos para aprobar los presupuestos.',
            'tipo' => 'evento',
            'fecha_inicio' => Carbon::now(),
            'fecha_fin' => Carbon::now()->addDays(7),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}