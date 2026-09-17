<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        DB::table('clasif_respuesta')->insertOrIgnore([
            [
                'id_clasif_resp' => 'RESP-01',
                'descripcion' => 'Atendido favorablemente',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_clasif_resp' => 'RESP-02',
                'descripcion' => 'Atendido desfavorablemente',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('estado_resp_reclamo')->insertOrIgnore([
            [
                'id_estado_reclamo' => 1,
                'nombre_estado' => 'En evaluación',
                'descripcion' => 'La respuesta está siendo evaluada.',
            ],
            [
                'id_estado_reclamo' => 2,
                'nombre_estado' => 'Resuelto',
                'descripcion' => 'El reclamo fue atendido y cerrado.',
            ],
            [
                'id_estado_reclamo' => 3,
                'nombre_estado' => 'Improcedente',
                'descripcion' => 'El reclamo no procede.',
            ],
        ]);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
    }
}
