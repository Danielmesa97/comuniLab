<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('password123'); // Contraseña unificada para todos

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
        // Mantenemos tus 3 viviendas para las pruebas de delegación
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
        $presidenteId = DB::table('users')->insertGetId([
            'name' => 'Toni Presidente',
            'email' => 'admin@test.com',
            'password' => $password,
            'role' => 'presidente',
            'vivienda_id' => $vivienda1A,
            'activo' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $propietarioId = DB::table('users')->insertGetId([
            'name' => 'Laura Propietaria',
            'email' => 'laura@test.com',
            'password' => $password,
            'role' => 'propietario',
            'vivienda_id' => $vivienda1B,
            'activo' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $inquilinoId = DB::table('users')->insertGetId([
            'name' => 'Carlos Inquilino',
            'email' => 'carlos@test.com',
            'password' => $password,
            'role' => 'inquilino',
            'vivienda_id' => $vivienda2A,
            'activo' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        
        // El SuperAdmin que añadieron en dev
        DB::table('users')->insert([
            'name' => 'Toni SuperAdmin',
            'email' => 'superadmin@test.com',
            'password' => $password,
            'role' => 'superadmin',
            'vivienda_id' => null, // El superadmin no tiene vivienda
            'activo' => true,
            'created_at' => $now,
            'updated_at' => $now,
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

        $votacionActiva2Id = DB::table('votaciones')->insertGetId([
            'comunidad_id' => $comunidadId,
            'titulo' => 'Instalación de Placas Solares',
            'descripcion' => 'Votación para aprobar el presupuesto de energía renovable.',
            'estado' => 'activa',
            'fecha_limite' => $now->copy()->addDays(30),
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $votacionCerradaId = DB::table('votaciones')->insertGetId([
            'titulo' => 'Cambiar luces a LED',
            'descripcion' => 'Sustituir las bombillas del garaje por tecnología LED para ahorrar consumo.',
            'estado' => 'cerrada',
            'comunidad_id' => $comunidadId,
            'fecha_limite' => $now->copy()->subDays(5),
            'created_at' => $now->copy()->subDays(20), 'updated_at' => $now->copy()->subDays(5),
        ]);

        // ==========================================
        // 5. VOTOS (Con tus delegaciones)
        // ==========================================
        DB::table('votos')->insert([
            // Votos normales de la votación activa 1
            ['user_id' => $presidenteId, 'votacion_id' => $votacionActivaId, 'vivienda_id' => $vivienda1A, 'vivienda_delegada_id' => null, 'opcion' => 'si', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $propietarioId, 'votacion_id' => $votacionActivaId, 'vivienda_id' => $vivienda1B, 'vivienda_delegada_id' => null, 'opcion' => 'no', 'created_at' => $now, 'updated_at' => $now],
            
            // NUEVO: Voto delegado pendiente. (El Piso 2A delega en el Piso 1A)
            ['user_id' => $inquilinoId, 'votacion_id' => $votacionActivaId, 'vivienda_id' => $vivienda2A, 'vivienda_delegada_id' => $vivienda1A, 'opcion' => null, 'created_at' => $now, 'updated_at' => $now],

            // Voto de la votación activa 2
            ['user_id' => $presidenteId, 'votacion_id' => $votacionActiva2Id, 'vivienda_id' => $vivienda1A, 'vivienda_delegada_id' => null, 'opcion' => 'si', 'created_at' => $now, 'updated_at' => $now],

            // Votos de la votación cerrada
            ['user_id' => $presidenteId, 'votacion_id' => $votacionCerradaId, 'vivienda_id' => $vivienda1A, 'vivienda_delegada_id' => null, 'opcion' => 'si', 'created_at' => $now->copy()->subDays(10), 'updated_at' => $now->copy()->subDays(10)],
            ['user_id' => $inquilinoId, 'votacion_id' => $votacionCerradaId, 'vivienda_id' => $vivienda2A, 'vivienda_delegada_id' => null, 'opcion' => 'si', 'created_at' => $now->copy()->subDays(12), 'updated_at' => $now->copy()->subDays(12)],
        ]);

        // ==========================================
        // 6. INCIDENCIAS
        // ==========================================
        DB::table('incidencias')->insert([
            ['titulo' => 'Luz fundida en el pasillo', 'descripcion' => 'La luz del primer piso lleva dos días sin funcionar.', 'estado' => 'pendiente', 'user_id' => $propietarioId, 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
            ['titulo' => 'Gotera en el rellano principal', 'descripcion' => 'Filtración de agua detectada cerca del ascensor tras las últimas lluvias.', 'estado' => 'en_proceso', 'user_id' => $inquilinoId, 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ==========================================
        // 7. ANUNCIOS
        // ==========================================
        DB::table('anuncios')->insert([
            ['titulo' => 'Reunión Anual Extraordinaria', 'descripcion' => 'Convocatoria de vecinos para aprobar presupuestos.', 'tipo' => 'evento', 'fecha_inicio' => $now->copy()->subDays(1), 'fecha_fin' => $now->copy()->addDays(7), 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
            ['titulo' => 'Corte de agua', 'descripcion' => 'El próximo martes se cortará el agua de 10:00 a 14:00 por mantenimiento.', 'tipo' => 'aviso', 'fecha_inicio' => $now, 'fecha_fin' => $now->copy()->addDays(2), 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
            ['titulo' => 'Normativa de piscina', 'descripcion' => 'Se ha publicado el PDF con las nuevas normas de uso de la piscina comunitaria.', 'tipo' => 'documento', 'fecha_inicio' => $now, 'fecha_fin' => $now->copy()->addMonths(3), 'comunidad_id' => $comunidadId, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ==========================================
        // 8. SOLICITUDES
        // ==========================================
        DB::table('solicitudes')->insert([
            ['nombre' => 'Ana Nuevo Inquilino', 'email' => 'ana.nueva@test.com', 'role' => 'inquilino', 'vivienda_id' => $vivienda1B, 'comunidad_id' => $comunidadId, 'estado' => 'pendiente', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ==========================================
        // 9. INSTALACIONES Y RESERVAS (De dev)
        // ==========================================
        $padelId = DB::table('instalaciones')->insertGetId([
            'comunidad_id' => $comunidadId,
            'nombre' => 'Pista de Pádel',
            'descripcion' => 'Pista de cristal con iluminación LED.',
            'duracion_franja' => 90,
            'aforo_max' => 4,
            'icono' => '🏓',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('instalaciones')->insert([
            [
                'comunidad_id' => $comunidadId,
                'nombre' => 'Piscina Comunitaria',
                'descripcion' => 'Zona de baño y solárium.',
                'duracion_franja' => 120,
                'aforo_max' => 20,
                'icono' => '🏊',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'comunidad_id' => $comunidadId,
                'nombre' => 'Sala Social / Club',
                'descripcion' => 'Espacio cerrado climatizado para reuniones.',
                'duracion_franja' => 240,
                'aforo_max' => 30,
                'icono' => '🎉',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
        
        DB::table('reservas')->insert([
            [
                'instalacion_id' => $padelId,
                'user_id' => $presidenteId, 
                'fecha' => $now->toDateString(), 
                'franja_id' => 1, 
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}