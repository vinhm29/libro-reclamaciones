<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disa extends Model
{
    protected $table = 'disa';
    protected $primaryKey = 'cod_disa';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // No tiene campos fecha_creacion/fecha_modificacion

    protected $fillable = [
        'cod_disa', // Llave primaria manual
        'nombre'
    ];


    // Una DISA tiene muchas Redes
    public function redes(): HasMany
    {
        return $this->hasMany(Red::class, 'cod_disa', 'cod_disa');
    }
}

