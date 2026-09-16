<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Microred extends Model
{
    protected $table = 'microred';
    protected $primaryKey = 'cod_microred';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'cod_microred',     // LLave primaria manual
        'nombre',
        'cod_red'       // Llave foránea a la tabla red
    ];

    public function red(): BelongsTo
    {
        return $this->belongsTo(Red::class, 'cod_red', 'cod_red');
    }

    public function establecimientos(): HasMany
    {
        return $this->hasMany(Establecimiento::class, 'cod_microred', 'cod_microred');
    }
}
