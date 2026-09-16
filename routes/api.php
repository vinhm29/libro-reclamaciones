<?php

use App\Http\Controllers\API\ClasifReclamoController;
use App\Http\Controllers\API\EstablecimientoController;
use App\Http\Controllers\API\ReclamoController;
use App\Http\Controllers\API\RespuestaReclamoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Formulario Web en Angular para Ciudadanos)
|--------------------------------------------------------------------------
*/
// Registrar un nuevo reclamo
Route::post('/reclamos', [ReclamoController::class, 'store']);

// Lista de establecimientos activos para el formulario público
Route::get('/establecimientos', [EstablecimientoController::class, 'index']);

// Lista de códigos de clasificación activos para el formulario público
Route::get('/clasificaciones', [ClasifReclamoController::class, 'index']);

// Buscar los datos de una persona por tipo y número de documento
Route::get('/personas/buscar', [ReclamoController::class, 'buscar']);

// Actualizar los datos de una persona existente
Route::put('/personas/{n_documento}', [ReclamoController::class, 'actualizarPersona']);

// Consultar el estado de un reclamo específico
Route::get('/reclamos/{correlativo}', [ReclamoController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Rutas Privadas / Administrativas (Intranet de la Empresa)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    // Lista total de quejas/reclamos para el panel administrativo
    // URL Final: GET http://127.0.0
    Route::get('/reclamos', [ReclamoController::class, 'index']);

    // Registrar la respuesta oficial a un reclamo
    // URL Final: POST http://127.0.0
    Route::post('/respuestas', [RespuestaReclamoController::class, 'store']);

    // Catálogos para el formulario de respuesta administrativa
    Route::get('/clasificaciones-respuesta', [RespuestaReclamoController::class, 'clasificaciones']);
    Route::get('/estados-reclamo', [RespuestaReclamoController::class, 'estados']);

    // Consultar una respuesta oficial específica
    // URL Final: GET http://127.0.0/{correlativo}
    Route::get('/respuestas/{correlativo}', [RespuestaReclamoController::class, 'show']);

});
