<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoClasifReclamo extends Model
{
    protected $table = 'tipo_clasif_reclamo';
    protected $primaryKey = 'id_tipo_clasif_reclamo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_clasif_reclamo', // Llave primaria manual
        'descripción'
    ];

    public function clasificaciones(): HasMany
    {
        return $this->hasMany(ClasifReclamo::class, 'id_tipo_clasif', 'id_tipo_clasif_reclamo');
    }
}
