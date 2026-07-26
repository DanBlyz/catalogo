<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permiso extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'permisos';

    protected $fillable = [
        'nombre',
        'codigo',
        'modulo',
        'descripcion',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permiso_usuario', 'permiso_id', 'usuario_id')
                    ->withTimestamps();
    }
}
