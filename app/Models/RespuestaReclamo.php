<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaReclamo extends Model
{
    protected $table = 'respuesta_reclamo';
    protected $primaryKey = 'correlativo_reclamo'; // Comparte la llave primaria con el Reclamo
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'correlativo_reclamo', // Llave primaria y foránea a la vez
        'archivo_adjunto',
        'observaciones',
        'canal_notif',
        'fecha_notif',
        'activo',
        'id_clasif_resp', // Llave foránea
        'id_estado_reclamo' // Llave foránea
    ];

    // Regresa al Reclamo original
    public function reclamo(): BelongsTo
    {
        return $this->belongsTo(Reclamo::class, 'correlativo_reclamo', 'correlativo');
    }

    // Tipo de respuesta dada
    public function clasificacionRespuesta(): BelongsTo
    {
        return $this->belongsTo(ClasifRespuesta::class, 'id_clasif_resp', 'id_clasif_resp');
    }

    // Estado final (Atendido, En proceso, etc.)
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoRespReclamo::class, 'id_estado_reclamo', 'id_estado_reclamo');
    }
}
