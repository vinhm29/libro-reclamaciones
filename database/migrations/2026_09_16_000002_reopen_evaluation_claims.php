<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $correlativos = DB::table('respuesta_reclamo')
            ->where('id_estado_reclamo', 1)
            ->pluck('correlativo_reclamo');

        DB::table('reclamo')
            ->whereIn('correlativo', $correlativos)
            ->update(['activo' => true]);
    }

    public function down(): void
    {
        // No se revierte el estado operativo de reclamos ya evaluados.
    }
};