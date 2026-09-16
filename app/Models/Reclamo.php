<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reclamo extends Model
{
    protected $table = 'reclamo';
    protected $primaryKey = 'correlativo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'correlativo', // Llave primaria manual (Código único generado)
        'doc_afectado', // Llave foránea (DNI de la Persona)
        'n_establecimiento', // Llave foránea (Código del Establecimiento)
        'detalle_reclamo',
        'notif_correo',
        'activo',
        'id_clasif' // Llave foránea
    ];

    // Un reclamo pertenece a un Ciudadano (Persona)
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'doc_afectado', 'n_documento');
    }

    // Un reclamo se realiza en un Establecimiento
    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'n_establecimiento', 'cod_establecimiento');
    }

    // Un reclamo tiene una clasificación específica
    public function clasificacion(): BelongsTo
    {
        return $this->belongsTo(ClasifReclamo::class, 'id_clasif', 'codigo');
    }

    // Un reclamo puede (o no) tener una Respuesta única asociada
    public function respuesta(): HasOne
    {
        return $this->hasOne(RespuestaReclamo::class, 'correlativo_reclamo', 'correlativo');
    }
}
