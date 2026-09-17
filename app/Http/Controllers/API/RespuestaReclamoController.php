<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClasifRespuesta;
use App\Models\EstadoRespReclamo;
use App\Models\Reclamo;
use App\Models\RespuestaReclamo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RespuestaReclamoController extends Controller
{
    public function clasificaciones(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => ClasifRespuesta::query()
                ->where('activo', true)
                ->orderBy('descripcion')
                ->get(['id_clasif_resp', 'descripcion']),
        ]);
    }

    public function estados(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EstadoRespReclamo::query()
                ->orderBy('id_estado_reclamo')
                ->get(['id_estado_reclamo', 'nombre_estado', 'descripcion']),
        ]);
    }

    /**
     * Canal Administrativo: Registrar la respuesta oficial a un reclamo específico.
     * Endpoint: POST /api/admin/respuestas
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validación estricta mapeada con tu tabla 'respuesta_reclamo'
        $request->validate([
            'correlativo_reclamo' => 'required|string|exists:reclamo,correlativo', // PK y FK al mismo tiempo
            'archivo_adjunto' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'observaciones' => 'required|string', // Detalle de la resolución de la empresa
            'canal_notif' => 'nullable|string|max:25', // E.g., 'CORREO', 'DOMICILIO'
            'fecha_notif' => 'required|date_format:Y-m-d H:i:s', // Formato de fecha y hora
            'id_clasif_resp' => 'nullable|string|max:20|exists:clasif_respuesta,id_clasif_resp', // FK
            'id_estado_reclamo' => 'required|integer|exists:estado_resp_reclamo,id_estado_reclamo', // FK (E.g., 3 = RESUELTO)
        ]);

        $esEstadoEvaluacion = (int) $request->input('id_estado_reclamo') === 1;
        if (! $esEstadoEvaluacion && (! $request->hasFile('archivo_adjunto') || ! $request->filled('canal_notif') || ! $request->filled('id_clasif_resp'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Para cerrar el reclamo debes adjuntar un archivo, seleccionar un canal y una clasificación.',
            ], 422);
        }

        // Usamos una transacción para asegurar consistencia entre la respuesta y el estado del reclamo
        DB::beginTransaction();

        try {
            // 2. Verificar si el reclamo ya cuenta con una respuesta previa
            // Al ser una relación HasOne con llave primaria 'correlativo_reclamo', solo se permite una respuesta única.
            $respuestaExistente = RespuestaReclamo::where('correlativo_reclamo', $request->input('correlativo_reclamo'))->first();

            if ($respuestaExistente && (int) $respuestaExistente->id_estado_reclamo !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Este reclamo ya cuenta con una respuesta oficial registrada.',
                ], 400);
            }

            // 3. Crear la respuesta en SQLite mediante asignación masiva
            $rutaArchivo = $request->hasFile('archivo_adjunto')
                ? $request->file('archivo_adjunto')->store('respuestas', 'public')
                : null;

            $datosRespuesta = [
                'correlativo_reclamo' => $request->input('correlativo_reclamo'),
                'archivo_adjunto' => $rutaArchivo,
                'observaciones' => $request->input('observaciones'),
                'canal_notif' => $request->input('canal_notif'),
                'fecha_notif' => $request->input('fecha_notif'),
                'id_clasif_resp' => $request->input('id_clasif_resp'),
                'id_estado_reclamo' => $request->input('id_estado_reclamo'),
                'activo' => true,
            ];

            $respuesta = $respuestaExistente
                ? tap($respuestaExistente)->update($datosRespuesta)
                : RespuestaReclamo::create($datosRespuesta);

            // En evaluación mantiene el caso abierto; los demás estados lo cierran.
            Reclamo::where('correlativo', $request->input('correlativo_reclamo'))->update([
                'activo' => $esEstadoEvaluacion,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'La respuesta al reclamo ha sido registrada y notificada con éxito.',
                'data' => $respuesta,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo procesar el registro de la respuesta.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Canal Administrativo: Consultar/Visualizar una respuesta específica por su correlativo.
     * Endpoint: GET /api/admin/respuestas/{correlativo}
     */
    public function show($correlativo): JsonResponse
    {
        // Carga la respuesta incluyendo los datos del reclamo original usando Eloquent
        $respuesta = RespuestaReclamo::with(['reclamo', 'clasifRespuesta', 'estadoRespReclamo'])
            ->where('correlativo_reclamo', $correlativo)
            ->first();

        if (! $respuesta) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró ninguna respuesta oficial asociada a este correlativo.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $respuesta,
        ], 200);
    }
}
