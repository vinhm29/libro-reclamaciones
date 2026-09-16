<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Establecimiento;
use Illuminate\Http\JsonResponse;

class EstablecimientoController extends Controller
{
    public function index(): JsonResponse
    {
        $establecimientos = Establecimiento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get([
                'cod_establecimiento',
                'nombre',
                'direccion',
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $establecimientos,
        ]);
    }
}
