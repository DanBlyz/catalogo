<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre_razon_social',
        'cedula_nit_ruc',
        'telefono',
        'email',
        'direccion',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }
}
