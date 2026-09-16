<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Red extends Model
{
    protected $table = 'red';
    protected $primaryKey = 'cod_red';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'cod_red', // Llave primaria manual
        'nombre_red',
        'cod_disa' // Llave foránea
    ];

    // Relación inversa: Una Red pertenece a una DISA
    public function disa(): BelongsTo
    {
        return $this->belongsTo(Disa::class, 'cod_disa', 'cod_disa');
    }

    public function microredes(): HasMany
    {
        return $this->hasMany(Microred::class, 'cod_red', 'cod_red');
    }
}
