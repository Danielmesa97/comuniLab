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
        // 1. CREAR 1 COMUNIDAD
        $comunidadId = DB::table('comunidades')->insertGetId([
            'nombre' => 'Residencial Los Pinos',
            'descripcion' => 'Comunidad de vecinos de la zona norte',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. CREAR 2 VIVIENDAS
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

        // 3. CREAR 2 USUARIOS
        $user1Id = DB::table('users')->insertGetId([
            'name' => 'Toni Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin', 
            'vivienda_id' => $vivienda1Id,
            'activo' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $user2Id = DB::table('users')->insertGetId([
            'name' => 'Laura Inquilina',
            'email' => 'laura@test.com',
            'password' => Hash::make('password123'),
            'role' => 'propietario',
            'vivienda_id' => $vivienda2Id,
            'activo' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        DB::table('users')->insert([
            'name' => 'Toni SuperAdmin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin', // 🌟 Aquí está la clave
            'vivienda_id' => null, // Seguramente el superadmin no tenga vivienda asignada
            'activo' => true,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        // 4. CREAR 2 INCIDENCIAS
        DB::table('incidencias')->insert([
            [
                'comunidad_id' => $comunidadId, // 🌟 AÑADIDO: Vital para el IncidenciaController
                'titulo' => 'Luz fundida en el pasillo',
                'descripcion' => 'La luz del primer piso lleva dos días sin funcionar.',
                'estado' => 'pendiente',
                'user_id' => $user1Id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comunidad_id' => $comunidadId, // 🌟 AÑADIDO
                'titulo' => 'Gotera en el rellano principal',
                'descripcion' => 'Filtración de agua detectada cerca del ascensor tras las últimas lluvias.',
                'estado' => 'en_proceso',
                'user_id' => $user2Id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 5. CREAR 3 VOTACIONES (2 Activas, 1 Inactiva)
        $votacionActiva1 = DB::table('votaciones')->insertGetId([
            'comunidad_id' => $comunidadId, // 🌟 AÑADIDO: Vital para el VotacionController
            'titulo' => 'Pintar la fachada del edificio',
            'descripcion' => 'Se propone pintar la fachada de color blanco.',
            'estado' => 'activa',
            'fecha_limite' => Carbon::now()->addDays(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $votacionActiva2 = DB::table('votaciones')->insertGetId([
            'comunidad_id' => $comunidadId, // 🌟 AÑADIDO
            'titulo' => 'Instalación de Placas Solares',
            'descripcion' => 'Votación para aprobar el presupuesto de energía renovable.',
            'estado' => 'activa',
            'fecha_limite' => Carbon::now()->addDays(30),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $votacionInactiva = DB::table('votaciones')->insertGetId([
            'comunidad_id' => $comunidadId, // 🌟 AÑADIDO
            'titulo' => 'Cambio de la puerta del garaje',
            'descripcion' => 'Presupuesto para sustituir el motor antiguo.',
            'estado' => 'cerrada', // Asegúrate de que coincida con tu enum de base de datos
            'fecha_limite' => Carbon::now()->subDays(5),
            'created_at' => Carbon::now()->subDays(20),
            'updated_at' => Carbon::now(),
        ]);

        // Votos de ejemplo
        DB::table('votos')->insert([
            [
                'user_id' => $user1Id,
                'votacion_id' => $votacionActiva1,
                'opcion' => 'si',
                'vivienda_id' => $vivienda1Id, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 6. CREAR 3 ANUNCIOS
        DB::table('anuncios')->insert([
            [
                'comunidad_id' => $comunidadId,
                'titulo' => 'Reunión Anual Extraordinaria',
                'descripcion' => 'Convocatoria de vecinos para aprobar presupuestos.',
                'tipo' => 'evento',
                'fecha_inicio' => Carbon::now()->subDay(),
                'fecha_fin' => Carbon::now()->addDays(7),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comunidad_id' => $comunidadId,
                'titulo' => 'Corte de agua temporal',
                'descripcion' => 'Corte programado de 10:00 a 13:00 por mantenimiento.',
                'tipo' => 'aviso',
                'fecha_inicio' => Carbon::now(),
                'fecha_fin' => Carbon::now()->addDays(2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comunidad_id' => $comunidadId,
                'titulo' => 'Nuevas normas de la Piscina',
                'descripcion' => 'Actualización del reglamento de uso del recinto.',
                'tipo' => 'noticia',
                'fecha_inicio' => Carbon::now(),
                'fecha_fin' => Carbon::now()->addMonth(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 7. CREAR 3 INSTALACIONES
        $padelId = DB::table('instalaciones')->insertGetId([
            'comunidad_id' => $comunidadId,
            'nombre' => 'Pista de Pádel',
            'descripcion' => 'Pista de cristal con iluminación LED.',
            'duracion_franja' => 90,
            'aforo_max' => 4,
            'icono' => '🏓',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $piscinaId = DB::table('instalaciones')->insertGetId([
            'comunidad_id' => $comunidadId,
            'nombre' => 'Piscina Comunitaria',
            'descripcion' => 'Zona de baño y solárium.',
            'duracion_franja' => 120,
            'aforo_max' => 20,
            'icono' => '🏊',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $salaId = DB::table('instalaciones')->insertGetId([
            'comunidad_id' => $comunidadId,
            'nombre' => 'Sala Social / Club',
            'descripcion' => 'Espacio cerrado climatizado para reuniones.',
            'duracion_franja' => 240,
            'aforo_max' => 30,
            'icono' => '🎉',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // Reservas de prueba
        DB::table('reservas')->insert([
            [
                'instalacion_id' => $padelId,
                'user_id' => $user1Id, 
                'fecha' => Carbon::now()->toDateString(), 
                'franja_id' => 1, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}