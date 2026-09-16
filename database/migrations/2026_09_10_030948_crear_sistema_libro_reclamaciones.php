<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. DISA
        Schema::create('disa', function (Blueprint $table) {
            $table->string('cod_disa', 50)->primary();
            $table->text('nombre');
        });

        // 2. RED
        Schema::create('red', function (Blueprint $table) {
            $table->string('cod_red', 50)->primary();
            $table->text('nombre_red');
            $table->string('cod_disa', 50);
            
            $table->foreign('cod_disa')->references('cod_disa')->on('disa')->onDelete('cascade');
        });

        // 3. MICRORED
        Schema::create('microred', function (Blueprint $table) {
            $table->string('cod_microred', 50)->primary();
            $table->text('nombre');
            $table->string('cod_red', 50);
            
            $table->foreign('cod_red')->references('cod_red')->on('red')->onDelete('cascade');
        });

        // 4. ESTABLECIMIENTO
        Schema::create('establecimiento', function (Blueprint $table) {
            $table->string('cod_establecimiento', 50)->primary();
            $table->text('nombre');
            $table->string('cod_microred', 50);
            $table->text('direccion')->nullable();
            $table->boolean('activo')->nullable();
            $table->timestamps(); // Genera automáticamente fecha_creacion y fecha_modificacion
            
            $table->foreign('cod_microred')->references('cod_microred')->on('microred')->onDelete('cascade');
        });

        // 5. TIPO CLASIF RECLAMO
        Schema::create('tipo_clasif_reclamo', function (Blueprint $table) {
            $table->string('id_tipo_clasif_reclamo', 15)->primary();
            $table->text('descripción');
        });

        // 6. CLASIF RECLAMO
        Schema::create('clasif_reclamo', function (Blueprint $table) {
            $table->string('codigo', 15)->primary();
            $table->text('descripcion');
            $table->boolean('activo')->nullable();
            $table->string('id_tipo_clasif', 15);
            $table->timestamps();
            
            $table->foreign('id_tipo_clasif')->references('id_tipo_clasif_reclamo')->on('tipo_clasif_reclamo')->onDelete('cascade');
        });

        // 7. CLASIF RESPUESTA
        Schema::create('clasif_respuesta', function (Blueprint $table) {
            $table->string('id_clasif_resp', 20)->primary();
            $table->text('descripcion');
            $table->boolean('activo')->nullable();
            $table->timestamps();
        });

        // 8. ESTADO RESP RECLAMO
        Schema::create('estado_resp_reclamo', function (Blueprint $table) {
            $table->unsignedTinyInteger('id_estado_reclamo')->primary();
            $table->string('nombre_estado', 30);
            $table->text('descripcion')->nullable();
        });

        // 9. PERSONA
        Schema::create('persona', function (Blueprint $table) {
            $table->string('n_documento', 10)->primary();
            $table->string('tipo_documento', 10);
            $table->text('nombre_completo');
            $table->text('domicilio')->nullable();
            $table->text('referencia')->nullable();
            $table->text('correo')->nullable();
            $table->string('telefono', 9)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->boolean('activo')->nullable();
            $table->timestamps();
        });

        // 10. RECLAMO
        Schema::create('reclamo', function (Blueprint $table) {
            $table->string('correlativo', 50)->primary();
            $table->string('doc_afectado', 10);
            $table->string('n_establecimiento', 50);
            $table->text('detalle_reclamo')->nullable();
            $table->char('notif_correo', 2)->nullable();
            $table->boolean('activo')->nullable();
            $table->string('id_clasif', 15);
            $table->timestamps();

            $table->foreign('doc_afectado')->references('n_documento')->on('persona')->onDelete('cascade');
            $table->foreign('n_establecimiento')->references('cod_establecimiento')->on('establecimiento')->onDelete('cascade');
            $table->foreign('id_clasif')->references('codigo')->on('clasif_reclamo')->onDelete('cascade');
        });

        // 11. RESPUESTA RECLAMO
        Schema::create('respuesta_reclamo', function (Blueprint $table) {
            $table->string('correlativo_reclamo', 50)->primary();
            $table->text('archivo_adjunto');
            $table->text('observaciones');
            $table->string('canal_notif', 25);
            $table->dateTime('fecha_notif');
            $table->boolean('activo')->nullable();
            $table->string('id_clasif_resp', 20);
            $table->unsignedTinyInteger('id_estado_reclamo');
            $table->timestamps();

            $table->foreign('correlativo_reclamo')->references('correlativo')->on('reclamo')->onDelete('cascade');
            $table->foreign('id_clasif_resp')->references('id_clasif_resp')->on('clasif_respuesta')->onDelete('cascade');
            $table->foreign('id_estado_reclamo')->references('id_estado_reclamo')->on('estado_resp_reclamo')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuesta_reclamo');
        Schema::dropIfExists('reclamo');
        Schema::dropIfExists('persona');
        Schema::dropIfExists('estado_resp_reclamo');
        Schema::dropIfExists('clasif_respuesta');
        Schema::dropIfExists('clasif_reclamo');
        Schema::dropIfExists('tipo_clasif_reclamo');
        Schema::dropIfExists('establecimiento');
        Schema::dropIfExists('microred');
        Schema::dropIfExists('red');
        Schema::dropIfExists('disa');
    }
};
