<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClasifReclamo;
use Illuminate\Http\JsonResponse;

class ClasifReclamoController extends Controller
{
    public function index(): JsonResponse
    {
        $clasificaciones = ClasifReclamo::query()
            ->where('activo', true)
            ->orderBy('codigo')
            ->get([
                'codigo',
                'descripcion',
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $clasificaciones,
        ]);
    }
}