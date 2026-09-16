<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $table = 'persona';
    protected $primaryKey = 'n_documento';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Campos habilitados para asignación masiva desde el formulario
    protected $fillable = [
        'n_documento',        // Obligatorio aquí porque se ingresa manualmente (DNI/CE/Pasaporte)
        'tipo_documento',     // DNI, CE, etc.
        'nombre_completo',
        'domicilio',
        'referencia',
        'correo',
        'telefono',
        'fecha_nacimiento',
        'activo'
    ];

    public function reclamos(): HasMany
    {
        return $this->hasMany(Reclamo::class, 'doc_afectado', 'n_documento');
    }
}
