<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'productos';

    protected $fillable = [
        'codigo_barras',
        'sku',
        'nombre',
        'descripcion',
        'categoria_id',
        'marca_id',
        'proveedor_id',
        'precio_compra',
        'precio_venta',
        'stock_minimo',
        'unidad_medida',
        'imagen',
        'estado',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function productoSucursales(): HasMany
    {
        return $this->hasMany(ProductoSucursal::class, 'producto_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    public function detallesVentas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }

    public function getStockSucursal(?int $sucursalId = null): float
    {
        if (!$sucursalId) {
            return (float) $this->productoSucursales()->sum('stock_actual');
        }

        $ps = $this->productoSucursales()->where('sucursal_id', $sucursalId)->first();
        return $ps ? (float) $ps->stock_actual : 0.0;
    }
}
