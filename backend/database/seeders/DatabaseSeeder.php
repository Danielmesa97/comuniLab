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
        $now = Carbon::now();

        // ==========================================
        // 1. COMUNIDADES
        // ==========================================
        $comunidadId = DB::table('comunidades')->insertGetId([
            'nombre' => 'Residencial Los Pinos',
            'descripcion' => 'Comunidad de vecinos de la zona norte con piscina y jardín.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ==========================================
        // 2. VIVIENDAS
        // ==========================================
        $vivienda1A = DB::table('viviendas')->insertGetId([
            'nombre' => '1º A',
            'comunidad_id' => $comunidadId,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $vivienda1B = DB::table('viviendas')->insertGetId([
            'nombre' => '1º B',
            'comunidad_id' => $comunidadId,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $vivienda2A = DB::table('viviendas')->insertGetId([
            'nombre' => '2º A',
            'comunidad_id' => $comunidadId,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // ==========================================
        // 3. USUARIOS
        // ==========================================
        $password = Hash::make('password123'); // Contraseña para todos en desarrollo

        $presidenteId = DB::table('users')->insertGetId([
            'name' => 'Toni Presidente',
            'email' => 'admin@test.com',
            'password' => $password123, // Corregido: antes ponía $password123
            'role' => 'presidente',
            'vivienda_id' => $vivienda1A,
            'activo' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $propietarioId = DB::table('users')->insertGetId([
            'name' => 'Laura Propietaria',
            'email' => 'laura@test.com',
            'password' => $password123, // Corregido
            'role' => 'propietario',
            'vivienda_id' => $vivienda1B,
            'activo' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $inquilinoId = DB::table('users')->insertGetId([
            'name' => 'Carlos Inquilino',
            'email' => 'carlos@test.com',
            'password' => $password123, // Corregido
            'role' => 'inquilino',
            'vivienda_id' => $vivienda2A,
            'activo' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // ==========================================
        // 4. VOTACIONES
        // ==========================================
        $votacionActivaId = DB::table('votaciones')->insertGetId([
            'titulo' => 'Pintar la fachada del edificio',
            'descripcion' => 'Se propone pintar la fachada de color blanco mate para mejorar la estética.',
            'estado' => 'activa',
            'comunidad_id' => $comunidadId,
            'fecha_limite' => $now->copy()->addDays(15),
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $votacionCerradaId = DB::table('votaciones')->insertGetId([
            'titulo' => 'Cambiar luces a LED',
            'descripcion' => 'Sustituir las bombillas del garaje por tecnología LED para ahorrar consumo.',
            'estado' => 'cerrada',
            'comunidad_id' => $comunidadId,
            'fecha_limite' => $now->copy()->subDays(5), // Caducó hace 5 días
            'created_at' => $now->copy()->subDays(20), 'updated_at' => $now->copy()->subDays(5),
        ]);

        // ==========================================
        // 5. VOTOS (ACTUALIZADO CON DELEGACIONES)
        // ==========================================
        DB::table('votos')->insert([
            // Votos normales de la votación activa
            ['user_id' => $presidenteId, 'votacion_id' => $votacionActivaId, 'vivienda_id' => $vivienda1A, 'vivienda_delegada_id' => null, 'opcion' => 'si', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $propietarioId, 'votacion_id' => $votacionActivaId, 'vivienda_id' => $vivienda1B, 'vivienda_delegada_id' => null, 'opcion' => 'no', 'created_at' => $now, 'updated_at' => $now],
            
            // NUEVO: Voto delegado pendiente. (El Piso 2A delega en el Piso 1A)
            ['user_id' => $inquilinoId, 'votacion_id' => $votacionActivaId, 'vivienda_id' => $vivienda2A, 'vivienda_delegada_id' => $vivienda1A, 'opcion' => null, 'created_at' => $now, 'updated_at' => $now],

            // Votos de la votación cerrada
            ['user_id' => $presidenteId, 'votacion_id' => $votacionCerradaId, 'vivienda_id' => $vivienda1A, 'vivienda_delegada_id' => null, 'opcion' => 'si', 'created_at' => $now->copy()->subDays(10), 'updated_at' => $now->copy()->subDays(10)],
            ['user_id' => $inquilinoId, 'votacion_id' => $votacionCerradaId, 'vivienda_id' => $vivienda2A, 'vivienda_delegada_id' => null, 'opcion' => 'si', 'created_at' => $now->copy()->subDays(12), 'updated_at' => $now->copy()->subDays(12)],
        ]);

        // ==========================================
        // 6. INCIDENCIAS
        // ==========================================
        DB::table('incidencias')->insert([
            ['titulo' => 'Luz fundida en el pasillo', 'descripcion' => 'La luz del primer piso lleva dos días sin funcionar.', 'estado' => 'pendiente', 'user_id' => $propietarioId, 'comunidad_id' => $comunidadId, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
            ['titulo' => 'Puerta del garaje atascada', 'descripcion' => 'Hace un ruido extraño al subir y a veces se para.', 'estado' => 'en proceso', 'user_id' => $inquilinoId, 'comunidad_id' => $comunidadId, 'foto' => null, 'created_at' => $now->copy()->subDays(2), 'updated_at' => $now->copy()->subDays(1)],
        ]);

        // ==========================================
        // 7. ANUNCIOS
        // ==========================================
        DB::table('anuncios')->insert([
            ['titulo' => 'Reunión Anual', 'descripcion' => 'Reunión de vecinos para aprobar los presupuestos del próximo año.', 'tipo' => 'evento', 'fecha_inicio' => $now->copy()->addDays(5), 'fecha_fin' => $now->copy()->addDays(5), 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
            ['titulo' => 'Corte de agua', 'descripcion' => 'El próximo martes se cortará el agua de 10:00 a 14:00 por mantenimiento.', 'tipo' => 'aviso', 'fecha_inicio' => $now->copy()->addDays(2), 'fecha_fin' => $now->copy()->addDays(2), 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
            ['titulo' => 'Normativa de piscina', 'descripcion' => 'Se ha publicado el PDF con las nuevas normas de uso de la piscina comunitaria.', 'tipo' => 'documento', 'fecha_inicio' => $now, 'fecha_fin' => $now->copy()->addMonths(3), 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ==========================================
        // 8. SOLICITUDES
        // ==========================================
        DB::table('solicitudes')->insert([
            ['nombre' => 'Ana Nuevo Inquilino', 'email' => 'ana.nueva@test.com', 'role' => 'inquilino', 'vivienda_id' => $vivienda1B, 'comunidad_id' => $comunidadId, 'estado' => 'pendiente', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}