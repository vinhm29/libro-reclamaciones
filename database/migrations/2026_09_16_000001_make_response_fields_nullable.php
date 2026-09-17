<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('respuesta_reclamo', function (Blueprint $table): void {
            $table->text('archivo_adjunto')->nullable()->change();
            $table->string('canal_notif', 25)->nullable()->change();
            $table->string('id_clasif_resp', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('respuesta_reclamo', function (Blueprint $table): void {
            $table->text('archivo_adjunto')->nullable(false)->change();
            $table->string('canal_notif', 25)->nullable(false)->change();
            $table->string('id_clasif_resp', 20)->nullable(false)->change();
        });
    }
};