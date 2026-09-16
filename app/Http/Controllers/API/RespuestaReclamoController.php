<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RespuestaReclamo;
use App\Models\Reclamo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RespuestaReclamoController extends Controller
{
    /**
     * Canal Administrativo: Registrar la respuesta oficial a un reclamo específico.
     * Endpoint: POST /api/admin/respuestas
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validación estricta mapeada con tu tabla 'respuesta_reclamo'
        $request->validate([
            'correlativo_reclamo' => 'required|string|exists:reclamo,correlativo', // PK y FK al mismo tiempo
            'archivo_adjunto'     => 'required|string', // Ruta del PDF o documento sustentatorio
            'observaciones'       => 'required|string', // Detalle de la resolución de la empresa
            'canal_notif'         => 'required|string|max:25', // E.g., 'CORREO', 'DOMICILIO'
            'fecha_notif'         => 'required|date_format:Y-m-d H:i:s', // Formato de fecha y hora
            'id_clasif_resp'      => 'required|string|max:20|exists:clasif_respuesta,id_clasif_resp', // FK
            'id_estado_reclamo'   => 'required|integer|exists:estado_resp_reclamo,id_estado_reclamo', // FK (E.g., 3 = RESUELTO)
        ]);

        // Usamos una transacción para asegurar consistencia entre la respuesta y el estado del reclamo
        DB::beginTransaction();

        try {
            // 2. Verificar si el reclamo ya cuenta con una respuesta previa
            // Al ser una relación HasOne con llave primaria 'correlativo_reclamo', solo se permite una respuesta única.
            $existeRespuesta = RespuestaReclamo::where('correlativo_reclamo', $request->input('correlativo_reclamo'))->exists();

            if ($existeRespuesta) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Este reclamo ya cuenta con una respuesta oficial registrada.'
                ], 400);
            }

            // 3. Crear la respuesta en SQLite mediante asignación masiva
            $respuesta = RespuestaReclamo::create([
                'correlativo_reclamo' => $request->input('correlativo_reclamo'),
                'archivo_adjunto'     => $request->input('archivo_adjunto'),
                'observaciones'       => $request->input('observaciones'),
                'canal_notif'         => $request->input('canal_notif'),
                'fecha_notif'         => $request->input('fecha_notif'),
                'id_clasif_resp'      => $request->input('id_clasif_resp'),
                'id_estado_reclamo'   => $request->input('id_estado_reclamo'),
                'activo'              => true
            ]);

            // 4. Cambiar el estado del reclamo de forma automática en la tabla 'reclamo'
            // Opcional: Si deseas marcar 'activo' como false al finalizar el flujo del reclamo.
            Reclamo::where('correlativo', $request->input('correlativo_reclamo'))->update([
                'activo' => false // Se da por concluido el ciclo de atención del reclamo
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'La respuesta al reclamo ha sido registrada y notificada con éxito.',
                'data'    => $respuesta
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo procesar el registro de la respuesta.',
                'error'   => $e->getMessage()
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

        if (!$respuesta) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se encontró ninguna respuesta oficial asociada a este correlativo.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $respuesta
        ], 200);
    }
}
