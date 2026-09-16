<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClasifRespuesta extends Model
{
    protected $table = 'clasif_respuesta';
    protected $primaryKey = 'id_clasif_resp';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';

    protected $fillable = [
        'id_clasif_resp', // Llave primaria manual
        'descripcion',
        'activo'
    ];

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaReclamo::class, 'id_clasif_resp', 'id_clasif_resp');
    }
}
