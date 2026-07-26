<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_empresa',
        'contacto_nombre',
        'nit_ruc',
        'telefono',
        'email',
        'direccion',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'proveedor_id');
    }
}
