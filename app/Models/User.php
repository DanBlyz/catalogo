<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Auditable, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'cedula',
        'telefono',
        'direccion',
        'email',
        'password',
        'rol_id',
        'sucursal_id',
        'estado',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'permiso_usuario', 'usuario_id', 'permiso_id')
            ->withTimestamps();
    }

    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class, 'usuario_id');
    }

    public function esAdmin(): bool
    {
        return $this->rol_id === 1 || ($this->rol && $this->rol->codigo === 'ADMIN');
    }

    public function tienePermiso(string $codigo): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        return $this->permisos()->where('codigo', $codigo)->exists();
    }

    public function tieneAlgunPermiso(array $codigos): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        return $this->permisos()->whereIn('codigo', $codigos)->exists();
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}") ?: ($this->name ?? '');
    }
}
