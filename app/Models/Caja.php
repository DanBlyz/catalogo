<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'cajas';

    protected $fillable = [
        'sucursal_id',
        'usuario_id',
        'monto_apertura',
        'monto_cierre',
        'ventas_efectivo',
        'ventas_digital',
        'total_esperado',
        'diferencia',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    protected $casts = [
        'monto_apertura' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
        'ventas_efectivo' => 'decimal:2',
        'ventas_digital' => 'decimal:2',
        'total_esperado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'caja_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'caja_id');
    }

    public static function cajaAbiertaDelUsuario(int $usuarioId): ?self
    {
        return static::where('usuario_id', $usuarioId)
            ->where('estado', 'ABIERTA')
            ->first();
    }
}
