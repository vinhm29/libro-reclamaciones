<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoRespReclamo extends Model
{
    protected $table = 'estado_resp_reclamo';
    protected $primaryKey = 'id_estado_reclamo';
    public $incrementing = false; // Es número, pero lo asignas tú manualmente
    public $timestamps = false;

    protected $fillable = [
        'id_estado_reclamo', // Llave primaria manual
        'nombre_estado',
        'descripcion'
    ];

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaReclamo::class, 'id_estado_reclamo', 'id_estado_reclamo');
    }
}
