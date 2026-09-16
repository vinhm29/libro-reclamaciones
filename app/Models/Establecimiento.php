<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Establecimiento extends Model
{
    protected $table = 'establecimiento';
    protected $primaryKey = 'cod_establecimiento';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';

    protected $fillable = [
        'cod_establecimiento', // Llave primaria manual
        'nombre',
        'cod_microred',     // Llave foránea a la tabla microred
        'direccion',
        'activo'
    ];

    public function microred(): BelongsTo
    {
        return $this->belongsTo(Microred::class, 'cod_microred', 'cod_microred');
    }

    public function reclamos(): HasMany
    {
        return $this->hasMany(Reclamo::class, 'n_establecimiento', 'cod_establecimiento');
    }
}
