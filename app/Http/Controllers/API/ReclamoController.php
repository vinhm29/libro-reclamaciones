<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reclamo;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReclamoController extends Controller
{
    /**
     * Canal Público: Registra el ciudadano y guarda el reclamo en la BD.
     * Endpoint: POST /api/reclamos
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validación estricta basada en tus columnas
        $request->validate([
            // Datos del Ciudadano (Persona)
            'n_documento'        => 'required|string|max:10',
            'tipo_documento'     => 'required|string|max:10',
            'nombre_completo'    => 'required|string',
            'domicilio'          => 'nullable|string',
            'referencia'         => 'nullable|string',
            'correo'             => 'nullable|email',
            'telefono'           => 'nullable|string|max:9',
            'fecha_nacimiento'   => 'nullable|date',

            // Datos del Reclamo
            'n_establecimiento'  => 'required|string|max:50|exists:establecimiento,cod_establecimiento',
            'detalle_reclamo'    => 'nullable|string',
            'notif_correo'       => 'nullable|string|max:2', // Ejemplo: 'SI' o 'NO'
            'id_clasif'          => 'required|string|max:15|exists:clasif_reclamo,codigo',
        ]);

        // Usamos una transacción de Base de Datos para asegurar que se guarden ambas tablas o ninguna
        DB::beginTransaction();

        try {
            // 2. Gestionar la Persona (Ciudadano)
            // Si el DNI ya existe, actualiza sus datos de contacto; si no, lo crea.
            $persona = Persona::updateOrCreate(
                ['n_documento' => $request->input('n_documento')],
                [
                    'tipo_documento'   => $request->input('tipo_documento'),
                    'nombre_completo'  => $request->input('nombre_completo'),
                    'domicilio'        => $request->input('domicilio'),
                    'referencia'       => $request->input('referencia'),
                    'correo'           => $request->input('correo'),
                    'telefono'         => $request->input('telefono'),
                    'fecha_nacimiento' => $request->input('fecha_nacimiento'),
                    'activo'           => true
                ]
            );

            // 3. Generar el correlativo único para el Reclamo
            // Formato estándar: REC - Año Actual - 6 caracteres aleatorios únicos
            $nuevoCorrelativo = 'REC-' . date('Y') . '-' . strtoupper(Str::random(6));

            // Asegurar que el correlativo no esté duplicado en SQLite
            while (Reclamo::where('correlativo', $nuevoCorrelativo)->exists()) {
                $nuevoCorrelativo = 'REC-' . date('Y') . '-' . strtoupper(Str::random(6));
            }

            // 4. Crear el Reclamo amarrado a las llaves foráneas de tus tablas
            $reclamo = Reclamo::create([
                'correlativo'       => $nuevoCorrelativo,
                'doc_afectado'      => $persona->n_documento, // FK a Persona
                'n_establecimiento' => $request->input('n_establecimiento'), // FK a Establecimiento
                'detalle_reclamo'   => $request->input('detalle_reclamo'),
                'notif_correo'      => $request->input('notif_correo') ?? 'NO',
                'id_clasif'         => $request->input('id_clasif'),
                'activo'            => true
            ]);

            DB::commit();

            // 5. Respuesta en formato JSON idóneo para Angular
            return response()->json([
                'status'  => 'success',
                'message' => 'Su reclamación ha sido registrada con éxito de forma interna.',
                'data'    => [
                    'correlativo'     => $reclamo->correlativo,
                    'ciudadano'       => $persona->nombre_completo,
                    'fecha_creacion'  => $reclamo->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Hubo un error al procesar el Libro de Reclamaciones.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Canal Administrativo: Listar todos los reclamos con sus relaciones (Eager Loading)
     * Endpoint: GET /api/admin/reclamos
     */
    public function index(): JsonResponse
    {
        // Trae los reclamos cargando en una sola consulta los datos de la Persona y el Establecimiento
        $reclamos = Reclamo::with(['persona', 'establecimiento', 'clasificacion'])
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'count'  => $reclamos->count(),
            'data'   => $reclamos
        ], 200);
    }

    /**
     * Canal Administrativo/Público: Ver el detalle de un reclamo por su correlativo
     * Endpoint: GET /api/reclamos/{correlativo}
     */
    public function show($correlativo): JsonResponse
    {
        $reclamo = Reclamo::with(['persona', 'establecimiento', 'clasificacion', 'respuesta'])
            ->where('correlativo', $correlativo)
            ->first();

        if (!$reclamo) {
            return response()->json([
                'status'  => 'error',
                'message' => 'El correlativo de reclamo ingresado no existe.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $reclamo
        ], 200);
    }
}
