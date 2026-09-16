<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClasifReclamo extends Model
{
    protected $table = 'clasif_reclamo';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';

    protected $fillable = [
        'codigo', // Llave primaria manual
        'descripcion',
        'activo',
        'id_tipo_clasif'        // Llave foránea a TipoClasifReclamo
    ];

    public function tipoClasificacion(): BelongsTo
    {
        return $this->belongsTo(TipoClasifReclamo::class, 'id_tipo_clasif', 'id_tipo_clasif_reclamo');
    }

    public function reclamos(): HasMany
    {
        return $this->hasMany(Reclamo::class, 'id_clasif', 'codigo');
    }
}
