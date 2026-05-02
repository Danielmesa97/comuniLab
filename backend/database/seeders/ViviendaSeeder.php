<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vivienda;
class ViviendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    Vivienda::create([
        'nombre' => 'Bajo A',
        'comunidad_id' => 1
    ]);

    Vivienda::create([
        'nombre' => '1ºB',
        'comunidad_id' => 1
    ]);

    Vivienda::create([
        'nombre' => '2ºA',
        'comunidad_id' => 1
    ]);
}
}