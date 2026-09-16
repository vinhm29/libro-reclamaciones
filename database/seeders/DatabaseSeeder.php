<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('disa')->insertOrIgnore([
            'cod_disa' => 'D01',
            'nombre' => 'Dirección de Salud',
        ]);
        DB::table('red')->insertOrIgnore([
            'cod_red' => 'R01',
            'nombre_red' => 'Red Principal',
            'cod_disa' => 'D01',
        ]);
        DB::table('microred')->insertOrIgnore([
            'cod_microred' => 'M01',
            'nombre' => 'Microred Principal',
            'cod_red' => 'R01',
        ]);
        DB::table('establecimiento')->insertOrIgnore([
            'cod_establecimiento' => 'E001',
            'nombre' => 'Establecimiento Principal',
            'cod_microred' => 'M01',
            'direccion' => 'Av. Principal 123',
            'activo' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('tipo_clasif_reclamo')->insertOrIgnore([
            'id_tipo_clasif_reclamo' => 'TIPO01',
            'descripción' => 'Clasificación general',
        ]);
        DB::table('clasif_reclamo')->insertOrIgnore([
            'codigo' => 'CLA-01',
            'descripcion' => 'Producto o servicio defectuoso',
            'activo' => true,
            'id_tipo_clasif' => 'TIPO01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
