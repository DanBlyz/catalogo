<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoInventario extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'sucursal_id',
        'producto_id',
        'tipo_movimiento',
        'cantidad',
        'precio_compra',
        'precio_venta',
        'stock_anterior',
        'stock_nuevo',
        'motivo',
        'referencia_tipo',
        'referencia_id',
        'fecha_movimiento',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_nuevo' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
